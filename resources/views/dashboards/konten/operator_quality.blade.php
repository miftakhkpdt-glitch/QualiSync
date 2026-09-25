{{-- File: dashboards/konten/operator_quality.blade.php --}}
{{-- KONTEN TENGAH OPERATOR QUALITY --}}

<!-- GRID CARD STATISTIK RINGKASAN INSPEKSI -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 20px;">

    <!-- Card 1: Incoming Material (Pending) -->
    <div style="background: #ffffff; padding: 18px 20px; border-radius: 10px; border: 1px solid #e2e8f0; border-left: 5px solid #0284c7; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="font-size: 11px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Incoming Pending</span>
            <h3 style="margin: 4px 0 0 0; font-size: 24px; font-weight: bold; color: #0f172a;">
                {{ $incomingPendingCount ?? 0 }}
            </h3>
            <span style="font-size: 11px; color: #0284c7; font-weight: 500;">Perlu Diperiksa</span>
        </div>
        <div style="width: 42px; height: 42px; border-radius: 8px; background: #e0f2fe; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-boxes" style="font-size: 20px; color: #0284c7;"></i>
        </div>
    </div>

    <!-- Card 2: In-Proses / QIR -->
    <div style="background: #ffffff; padding: 18px 20px; border-radius: 10px; border: 1px solid #e2e8f0; border-left: 5px solid #ec4899; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="font-size: 11px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Inspeksi In-Proses</span>
            <h3 style="margin: 4px 0 0 0; font-size: 24px; font-weight: bold; color: #0f172a;">
                {{ $inProsesCount ?? 0 }}
            </h3>
            <span style="font-size: 11px; color: #ec4899; font-weight: 500;">Aktif Hari Ini</span>
        </div>
        <div style="width: 42px; height: 42px; border-radius: 8px; background: #fce7f3; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-cogs" style="font-size: 20px; color: #ec4899;"></i>
        </div>
    </div>

    <!-- Card 3: COA Rilis -->
    <div style="background: #ffffff; padding: 18px 20px; border-radius: 10px; border: 1px solid #e2e8f0; border-left: 5px solid #10b981; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="font-size: 11px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">COA Diterbitkan</span>
            <h3 style="margin: 4px 0 0 0; font-size: 24px; font-weight: bold; color: #0f172a;">
                {{ $coaCount ?? 0 }}
            </h3>
            <span style="font-size: 11px; color: #10b981; font-weight: 500;">Bulan Ini</span>
        </div>
        <div style="width: 42px; height: 42px; border-radius: 8px; background: #d1fae5; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-certificate" style="font-size: 20px; color: #10b981;"></i>
        </div>
    </div>

</div>