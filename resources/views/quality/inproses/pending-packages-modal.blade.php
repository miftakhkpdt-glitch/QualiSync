@if($pendingPackages->isNotEmpty() && !$completionPackage)
    <dialog id="pending-packages-dialog" style="width: min(760px, calc(100% - 32px)); max-height: 85vh; border: 0; border-radius: 12px; padding: 24px; box-shadow: 0 20px 60px rgba(15, 23, 42, 0.3);">
        <h3 style="margin-top: 0; color: #9a3412;">Paket FG/QIR Belum Lengkap</h3>
        <p style="color: #475569;">Paket ini belum memiliki dokumen pasangan. Pilih “Lengkapi” untuk langsung mengisi dokumen yang kurang. Pop-up ini hanya sebagai pengingat; input FG/QIR baru tetap dapat dilakukan.</p>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background: #fff7ed; text-align: left;">
                        <th style="padding: 9px; border-bottom: 1px solid #fed7aa;">Tanggal</th>
                        <th style="padding: 9px; border-bottom: 1px solid #fed7aa;">No. Batch</th>
                        <th style="padding: 9px; border-bottom: 1px solid #fed7aa;">No. MM</th>
                        <th style="padding: 9px; border-bottom: 1px solid #fed7aa;">Shift</th>
                        <th style="padding: 9px; border-bottom: 1px solid #fed7aa;">Line</th>
                        <th style="padding: 9px; border-bottom: 1px solid #fed7aa;">Dokumen Kurang</th>
                        <th style="padding: 9px; border-bottom: 1px solid #fed7aa;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingPackages as $package)
                        @php
                            $targetUrl = $package->missing_document === 'QIR' ? url('/qir/create') : url('/in-proses/fg/create');
                            $completionUrl = $targetUrl . '?' . http_build_query([
                                'completion_from' => strtolower($package->source_type),
                                'completion_id' => $package->source_id,
                            ]);
                        @endphp
                        <tr>
                            <td style="padding: 9px; border-bottom: 1px solid #e2e8f0;">{{ $package->tanggal }}</td>
                            <td style="padding: 9px; border-bottom: 1px solid #e2e8f0;">{{ $package->no_batch }}</td>
                            <td style="padding: 9px; border-bottom: 1px solid #e2e8f0;">{{ $package->no_mm }}</td>
                            <td style="padding: 9px; border-bottom: 1px solid #e2e8f0;">{{ $package->shift }}</td>
                            <td style="padding: 9px; border-bottom: 1px solid #e2e8f0;">{{ $package->line_produksi }}</td>
                            <td style="padding: 9px; border-bottom: 1px solid #e2e8f0; font-weight: 700;">{{ $package->missing_document }}</td>
                            <td style="padding: 9px; border-bottom: 1px solid #e2e8f0;">
                                <a href="{{ $completionUrl }}" style="display: inline-block; background: #2563eb; color: #fff; text-decoration: none; padding: 7px 10px; border-radius: 6px; white-space: nowrap;">Lengkapi</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <form method="dialog" style="text-align: right; margin-top: 20px;">
            <button style="background: #64748b; color: #fff; border: 0; border-radius: 6px; padding: 9px 15px; cursor: pointer;">Tutup</button>
        </form>
    </dialog>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dialog = document.getElementById('pending-packages-dialog');
            if (dialog && typeof dialog.showModal === 'function') {
                dialog.showModal();
            }
        });
    </script>
@endif
