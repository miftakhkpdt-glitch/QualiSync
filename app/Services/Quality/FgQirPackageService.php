<?php

namespace App\Services\Quality;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FgQirPackageService
{
    private const PACKAGE_COLUMNS = [
        'tanggal',
        'no_batch',
        'no_mm',
        'shift',
        'line_produksi',
    ];

    public function pendingPackages(?string $line = null): Collection
    {
        $fgPackages = $this->unfinishedFromFg();
        $qirPackages = $this->unfinishedFromQir();

        if ($line !== null) {
            $fgPackages->where('fg.line_produksi', $line);
            $qirPackages->where('qir.line_produksi', $line);
        }

        $packages = $fgPackages->get()
            ->concat($qirPackages->get())
            ->sort(function ($first, $second) {
                return [$first->tanggal, $first->line_produksi, $first->no_batch]
                    <=> [$second->tanggal, $second->line_produksi, $second->no_batch];
            });

        return $packages->values();
    }

    public function findPendingPackage(string $sourceType, int $sourceId): ?object
    {
        if (!in_array($sourceType, ['FG', 'QIR'], true)) {
            throw new InvalidArgumentException('Jenis dokumen sumber paket tidak dikenal.');
        }

        $sourceTable = $sourceType === 'FG' ? 'fg_inspections' : 'qir_records';
        $record = DB::table($sourceTable)->where('id', $sourceId)->first();

        if (!$record) {
            return null;
        }

        $counterpartTable = $sourceType === 'FG' ? 'qir_records' : 'fg_inspections';
        $counterpartExists = DB::table($counterpartTable)
            ->where('tanggal', $record->tanggal)
            ->where('no_batch', $record->no_batch)
            ->where('no_mm', $record->no_mm)
            ->where('shift', $record->shift)
            ->where('line_produksi', $record->line_produksi)
            ->exists();

        if ($counterpartExists) {
            return null;
        }

        return (object) [
            'source_type' => $sourceType,
            'source_id' => $record->id,
            'missing_document' => $sourceType === 'FG' ? 'QIR' : 'FG',
            ...array_intersect_key((array) $record, array_flip(self::PACKAGE_COLUMNS)),
        ];
    }

    public function matchesPackage(object $pendingPackage, array $values): bool
    {
        foreach (self::PACKAGE_COLUMNS as $column) {
            if ((string) ($pendingPackage->{$column} ?? '') !== (string) ($values[$column] ?? '')) {
                return false;
            }
        }

        return true;
    }

    private function unfinishedFromFg(): Builder
    {
        return DB::table('fg_inspections as fg')
            ->selectRaw("'FG' as source_type, fg.id as source_id, 'QIR' as missing_document, fg.tanggal, fg.no_batch, fg.no_mm, fg.shift, fg.line_produksi")
            ->whereNotExists(function (Builder $query) {
                $query->selectRaw('1')
                    ->from('qir_records as qir')
                    ->whereColumn('qir.tanggal', 'fg.tanggal')
                    ->whereColumn('qir.no_batch', 'fg.no_batch')
                    ->whereColumn('qir.no_mm', 'fg.no_mm')
                    ->whereColumn('qir.shift', 'fg.shift')
                    ->whereColumn('qir.line_produksi', 'fg.line_produksi');
            });
    }

    private function unfinishedFromQir(): Builder
    {
        return DB::table('qir_records as qir')
            ->selectRaw("'QIR' as source_type, qir.id as source_id, 'FG' as missing_document, qir.tanggal, qir.no_batch, qir.no_mm, qir.shift, qir.line_produksi")
            ->whereNotExists(function (Builder $query) {
                $query->selectRaw('1')
                    ->from('fg_inspections as fg')
                    ->whereColumn('fg.tanggal', 'qir.tanggal')
                    ->whereColumn('fg.no_batch', 'qir.no_batch')
                    ->whereColumn('fg.no_mm', 'qir.no_mm')
                    ->whereColumn('fg.shift', 'qir.shift')
                    ->whereColumn('fg.line_produksi', 'qir.line_produksi');
            });
    }
}
