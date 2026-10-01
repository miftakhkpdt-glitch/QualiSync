@extends('layouts.staff-layout')

@section('title', 'Semua Log Aktivitas')

@push('styles')
<style>
    .activity-log-page { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
    .activity-log-header { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px; }
    .activity-log-title { margin: 0; color: #1e293b; font-size: 24px; }
    .activity-log-subtitle { margin: 6px 0 0; color: #64748b; font-size: 14px; }
    .activity-log-back { display: inline-flex; align-items: center; gap: 7px; padding: 9px 13px; border-radius: 6px; background: #64748b; color: #fff; text-decoration: none; white-space: nowrap; }
    .activity-log-card { overflow: hidden; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05); }
    .activity-log-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px 20px; border-bottom: 1px solid #e2e8f0; }
    .activity-log-count { margin: 0; color: #64748b; font-size: 13px; }
    .activity-log-search { display: flex; gap: 8px; width: min(100%, 420px); }
    .activity-log-search input { min-width: 0; flex: 1; padding: 9px 11px; border: 1px solid #cbd5e1; border-radius: 6px; font: inherit; }
    .activity-log-search button { padding: 9px 13px; border: 0; border-radius: 6px; background: #3b82f6; color: #fff; cursor: pointer; }
    .activity-log-table-wrap { overflow-x: auto; }
    .activity-log-table { width: 100%; border-collapse: collapse; text-align: left; }
    .activity-log-table th, .activity-log-table td { padding: 13px 16px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    .activity-log-table th { background: #f8fafc; color: #334155; font-size: 13px; }
    .activity-log-table td { color: #475569; font-size: 14px; }
    .activity-log-kind { display: inline-block; padding: 4px 8px; border-radius: 4px; background: #e0f2fe; color: #0369a1; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .activity-log-empty { padding: 32px 16px !important; text-align: center; color: #64748b !important; }
    .activity-log-pagination { padding: 16px 20px; }
    @media (max-width: 640px) {
        .activity-log-header, .activity-log-toolbar { align-items: stretch; flex-direction: column; }
        .activity-log-title { font-size: 21px; }
        .activity-log-search { width: 100%; }
    }
</style>
@endpush

@section('konten')
<main class="activity-log-page">
    <header class="activity-log-header">
        <div>
            <h1 class="activity-log-title"><i class="fas fa-history" style="color: #8b5cf6;"></i> Semua Log Aktivitas</h1>
            <p class="activity-log-subtitle">Riwayat terbaru COA, QIR, dan kedatangan incoming material.</p>
        </div>
        <a href="{{ route('quality.dashboard') }}" class="activity-log-back"><i class="fas fa-arrow-left"></i> Kembali</a>
    </header>

    <section class="activity-log-card" aria-label="Daftar log aktivitas">
        <div class="activity-log-toolbar">
            <p class="activity-log-count">
                Menampilkan {{ $logs->firstItem() ?? 0 }}-{{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} aktivitas
            </p>
            <form action="{{ route('quality.activity-logs') }}" method="GET" class="activity-log-search">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas, nomor, item, atau customer..." aria-label="Cari log aktivitas">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>
        </div>

        <div class="activity-log-table-wrap">
            <table class="activity-log-table">
                <thead>
                    <tr>
                        <th style="width: 175px;">Waktu</th>
                        <th style="width: 160px;">Jenis</th>
                        <th>Aktivitas</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->occurred_at ? \Carbon\Carbon::parse($log->occurred_at)->format('d-m-Y H:i') : '-' }}</td>
                            <td><span class="activity-log-kind">{{ $log->event_type }}</span></td>
                            <td><strong>{{ $log->title }}</strong></td>
                            <td>{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="activity-log-empty">Tidak ada aktivitas yang cocok dengan pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="activity-log-pagination">{{ $logs->links() }}</div>
        @endif
    </section>
</main>
@endsection