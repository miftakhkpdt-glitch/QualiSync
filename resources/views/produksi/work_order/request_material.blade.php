<!-- ... (Kode Info WO dan Tabel BOM sama seperti milik PPIC sebelumnya) ... -->

        @if(count($kebutuhanMaterial) > 0)
        <div style="margin-top: 25px; text-align: right; padding-top: 15px; border-top: 1px solid #e2e8f0;">
            <!-- FORM SUBMIT MENGARAH KE ROUTE PRODUKSI -->
            <form action="{{ route('produksi.work_order.submit', $wo->id) }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Kirim permintaan material ini ke Gudang?')" style="background: #10b981; color: white; border: none; padding: 12px 25px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px;">
                    <i class="fas fa-paper-plane"></i> Kirim Permintaan ke Gudang
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection