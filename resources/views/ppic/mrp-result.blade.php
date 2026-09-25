@extends('layouts.staff-layout')

@section('title', 'Dashboard Kalkulasi MRP - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; width: 100%; box-sizing: border-box;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; color: #1e293b; margin: 0;"><i class="fas fa-desktop" style="color: #0ea5e9;"></i> Monitor MRP Aktif</h2>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <form action="{{ url()->current() }}" method="GET" style="display: flex; gap: 5px; margin: 0;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Customer / Produk / MM..." 
                       style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; width: 250px; outline: none;">
                
                <button type="submit" style="background: #3b82f6; color: white; padding: 8px 15px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(59,130,246,0.2);">
                    <i class="fas fa-search"></i> Cari
                </button>
                
                @if(request('search'))
                    <a href="{{ url()->current() }}" style="background: #ef4444; color: white; padding: 8px 12px; border-radius: 4px; text-decoration: none; font-weight: bold; display: flex; align-items: center;" title="Hapus Filter">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            <button onclick="window.location.reload();" style="background: #10b981; color: white; padding: 8px 15px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(16,185,129,0.2);">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow-x: auto; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 12px; white-space: nowrap;">
            <thead>
                <tr style="background: #f8fafc;">
                    <th rowspan="2" style="padding: 12px; border-bottom: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0;">Customer / Produk</th>
                    <th rowspan="2" style="padding: 12px; border-bottom: 2px solid #cbd5e1; border-right: 2px solid #94a3b8; text-align: center;">Tipe</th>
                    
                    <th colspan="3" style="padding: 10px; text-align: center; background: #e0f2fe; color: #0369a1; border-right: 2px solid #94a3b8; border-bottom: 1px solid #bae6fd;">📦 KONTROL FINISH GOOD (FG)</th>
                    <th colspan="3" style="padding: 10px; text-align: center; background: #fef3c7; color: #b45309; border-right: 2px solid #94a3b8; border-bottom: 1px solid #fde68a;">📜 KONTROL PWEB</th>
                    <th colspan="3" style="padding: 10px; text-align: center; background: #e0e7ff; color: #4338ca; border-right: 2px solid #94a3b8; border-bottom: 1px solid #c7d2fe;">🧢 KONTROL CAP</th>
                    <th colspan="3" style="padding: 10px; text-align: center; background: #f3e8ff; color: #6b21a8; border-right: 2px solid #94a3b8; border-bottom: 1px solid #e9d5ff;">🛢️ KONTROL HDPE</th>
                    <th colspan="3" style="padding: 10px; text-align: center; background: #ecfdf5; color: #065f46; border-right: 2px solid #94a3b8; border-bottom: 1px solid #a7f3d0;">🎨 KONTROL MB</th>
                    <th colspan="3" style="padding: 10px; text-align: center; background: #f1f5f9; color: #475569; border-right: 2px solid #94a3b8; border-bottom: 1px solid #cbd5e1;">📦 KONTROL BOX</th>
                    <th colspan="3" style="padding: 10px; text-align: center; background: #fff1f2; color: #be123c; border-bottom: 1px solid #fecdd3;">🛍️ KONTROL PLASTIK GUSSET</th>
                </tr>
                <tr style="background: #f8fafc;">
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; background: #f0f9ff;">Stok vs ROP</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: center; background: #f0f9ff;">Status</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; border-right: 2px solid #94a3b8; text-align: right; background: #f0f9ff;">Action WO</th>

                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; background: #fffbeb;">Stok vs ROP</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: center; background: #fffbeb;">Status</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; border-right: 2px solid #94a3b8; text-align: right; background: #fffbeb;">Action PR</th>

                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; background: #eef2ff;">Stok vs ROP</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: center; background: #eef2ff;">Status</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; border-right: 2px solid #94a3b8; text-align: right; background: #eef2ff;">Action PR</th>

                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; background: #faf5ff;">Stok vs Butuh</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: center; background: #faf5ff;">Status</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; border-right: 2px solid #94a3b8; text-align: right; background: #faf5ff;">Action PR</th>

                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; background: #f0fdf4;">Stok vs Butuh</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: center; background: #f0fdf4;">Status</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; border-right: 2px solid #94a3b8; text-align: right; background: #f0fdf4;">Action PR</th>

                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; background: #f8fafc;">Stok vs Butuh</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: center; background: #f8fafc;">Status</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; border-right: 2px solid #94a3b8; text-align: right; background: #f8fafc;">Action PR / FU</th>

                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; background: #fff1f2;">Stok vs Butuh</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: center; background: #fff1f2;">Status</th>
                    <th style="padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: right; background: #fff1f2;">Action PR / FU</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mrpData ?? [] as $mrp)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                    <td style="padding: 12px; border-right: 1px solid #e2e8f0;">
                        <div style="font-weight: bold; color: #1e293b; font-size: 13px;">{{ $mrp->nama_customer }}</div>
                        <div style="color: #0284c7; font-weight: 600; margin-top: 2px; white-space: normal; min-width: 200px;">{{ $mrp->nama_produk }}</div>
                        <div style="color: #64748b; font-size: 11px;">MM: {{ $mrp->no_mm }}</div>
                    </td>
                    
                    <td style="padding: 12px; text-align: center; border-right: 2px solid #94a3b8;">
                        <span style="background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 10px;">{{ $mrp->tipe_kalkulasi }}</span>
                    </td>

                    @if($mrp->tipe_kalkulasi == 'Min-Max')
                        <!-- DATA FG -->
                        <td style="padding: 12px; background: #f8fafc;">
                            Stok: <b>{{ number_format($mrp->stok_fg) }}</b><br>
                            <span style="color: #64748b; font-size: 10px;">ROP: {{ number_format($mrp->rop_fg) }}</span><br>
                            <span style="color: #0284c7; font-size: 10px; font-weight: bold;">Max: {{ number_format($mrp->max_stock_fg) }}</span>
                        </td>
                        <td style="padding: 12px; text-align: center; background: #f8fafc;">
                            @if($mrp->status_fg == 'Shortage')
                                <span style="color: #dc2626; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> Shortage</span>
                            @else
                                <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> Aman</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: right; border-right: 2px solid #94a3b8; background: #f8fafc;">
                            @if($mrp->kebutuhan_wo > 0)
                                <div style="color: #dc2626; font-weight: bold; margin-bottom: 5px;">{{ number_format($mrp->kebutuhan_wo) }} Pcs</div>
                                @if($mrp->stok_pweb < $mrp->kebutuhan_wo)
                                    <button onclick="alert('🔒 TIDAK BISA MEMBUAT WO!\n\nStok material Printed Web tidak mencukupi.')" style="background: #94a3b8; color: white; border: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; cursor: not-allowed;"><i class="fas fa-lock"></i> Material Kurang</button>
                                @else
                                    <a href="{{ url('/ppic/create-wo') }}?no_mm={{ $mrp->no_mm }}&qty={{ $mrp->kebutuhan_wo }}" style="background: #0284c7; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; display: inline-block;"><i class="fas fa-plus-circle"></i> Buat WO</a>
                                @endif
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>

                        <!-- DATA PWEB -->
                        <td style="padding: 12px; background: #fffbeb;">
                            Stok: <b>{{ number_format($mrp->stok_pweb) }}</b><br>
                            <span style="color: #64748b; font-size: 10px;">ROP: {{ number_format($mrp->rop_pweb) }}</span><br>
                            <span style="color: #b45309; font-size: 10px; font-weight: bold;">Max: {{ number_format($mrp->max_stock_pweb) }}</span>
                        </td>
                        <td style="padding: 12px; text-align: center; background: #fffbeb;">
                            @if($mrp->status_pweb == 'Shortage')
                                <span style="color: #dc2626; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> Shortage</span>
                            @elseif($mrp->status_pweb == 'On Order')
                                <span style="color: #d97706; font-weight: bold;"><i class="fas fa-truck"></i> On Order</span>
                            @else
                                <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> Aman</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: right; border-right: 2px solid #94a3b8; background: #fffbeb;">
                            @if($mrp->kebutuhan_po > 0)
                                <div style="color: #b45309; font-weight: bold; margin-bottom: 5px;">{{ number_format($mrp->kebutuhan_po) }} Pcs</div>
                                <a href="{{ url('/ppic/create-pr') }}?no_mm={{ $mrp->pweb_mm ?? $mrp->no_mm }}&qty={{ $mrp->net_requirement ?? $mrp->kebutuhan_po }}" style="background: #8b5cf6; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; display: inline-block;"><i class="fas fa-file-signature"></i> Buat PR</a>
                            @elseif($mrp->status_pweb == 'On Order')
                                <span style="color: #d97706; font-size: 11px; font-weight: bold; background: #fef3c7; padding: 4px 8px; border-radius: 4px;"><i class="fas fa-clock"></i> Menunggu PR</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>

                        <!-- DATA CAP -->
                        <td style="padding: 12px; background: #eef2ff;">
                            Stok: <b>{{ number_format($mrp->stok_cap ?? 0) }}</b> Pcs<br>
                            <span style="color: #64748b; font-size: 10px;">ROP: {{ number_format($mrp->rop_cap ?? 0) }}</span><br>
                            <span style="color: #4338ca; font-size: 10px; font-weight: bold;">Max: {{ number_format($mrp->max_stock_cap ?? 0) }}</span>
                        </td>
                        <td style="padding: 12px; text-align: center; background: #eef2ff;">
                            @if(($mrp->status_cap ?? '') == 'Shortage')
                                <span style="color: #dc2626; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> Shortage</span>
                            @elseif(($mrp->status_cap ?? '') == 'On Order')
                                <span style="color: #d97706; font-weight: bold;"><i class="fas fa-truck"></i> On Order</span>
                            @else
                                <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> Aman</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: right; border-right: 2px solid #94a3b8; background: #eef2ff;">
                            @if(($mrp->pr_cap ?? 0) > 0)
                                <div style="color: #4338ca; font-weight: bold; margin-bottom: 5px;">{{ number_format($mrp->pr_cap) }} Pcs</div>
                                <a href="{{ url('/ppic/create-pr') }}?no_mm={{ $mrp->cap_mm }}&qty={{ $mrp->pr_cap }}" style="background: #8b5cf6; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; display: inline-block;"><i class="fas fa-file-signature"></i> Buat PR</a>
                            @elseif(($mrp->status_cap ?? '') == 'On Order')
                                <span style="color: #d97706; font-size: 11px; font-weight: bold; background: #fef3c7; padding: 4px 8px; border-radius: 4px;"><i class="fas fa-clock"></i> Menunggu PR</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>

                        <!-- DATA HDPE -->
                        <td style="padding: 12px; background: #faf5ff;">
                            Stok: <b>{{ number_format($mrp->stok_hdpe ?? 0, 2) }}</b> Kg<br>
                            <span style="color: #6b21a8; font-size: 10px; font-weight: bold;">Butuh: {{ number_format($mrp->kebutuhan_hdpe ?? 0, 2) }} Kg</span>
                        </td>
                        <td style="padding: 12px; text-align: center; background: #faf5ff;">
                            @if(($mrp->status_hdpe ?? '') == 'Shortage')
                                <span style="color: #dc2626; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> Shortage</span>
                            @elseif(($mrp->status_hdpe ?? '') == 'On Order')
                                <span style="color: #d97706; font-weight: bold;"><i class="fas fa-truck"></i> On Order</span>
                            @else
                                <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> Aman</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: right; border-right: 2px solid #94a3b8; background: #faf5ff;">
                            @if(($mrp->pr_hdpe ?? 0) > 0)
                                <div style="color: #6b21a8; font-weight: bold; margin-bottom: 5px;">{{ number_format($mrp->pr_hdpe, 2) }} Kg</div>
                                <a href="{{ url('/ppic/create-pr') }}?no_mm={{ $mrp->hdpe_mm }}&qty={{ $mrp->pr_hdpe }}" style="background: #8b5cf6; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; display: inline-block;"><i class="fas fa-file-signature"></i> Buat PR</a>
                            @elseif(($mrp->status_hdpe ?? '') == 'On Order')
                                <span style="color: #d97706; font-size: 11px; font-weight: bold; background: #fef3c7; padding: 4px 8px; border-radius: 4px;"><i class="fas fa-clock"></i> Menunggu PR</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>

                        <!-- DATA MASTER BATCH -->
                        <td style="padding: 12px; background: #f0fdf4;">
                            Stok: <b>{{ number_format($mrp->stok_mb ?? 0, 2) }}</b> Kg<br>
                            <span style="color: #065f46; font-size: 10px; font-weight: bold;">Butuh: {{ number_format($mrp->kebutuhan_mb ?? 0, 2) }} Kg</span>
                        </td>
                        <td style="padding: 12px; text-align: center; background: #f0fdf4;">
                            @if(($mrp->status_mb ?? '') == 'Shortage')
                                <span style="color: #dc2626; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> Shortage</span>
                            @elseif(($mrp->status_mb ?? '') == 'On Order')
                                <span style="color: #d97706; font-weight: bold;"><i class="fas fa-truck"></i> On Order</span>
                            @else
                                <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> Aman</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: right; border-right: 2px solid #94a3b8; background: #f0fdf4;">
                            @if(($mrp->pr_mb ?? 0) > 0)
                                <div style="color: #065f46; font-weight: bold; margin-bottom: 5px;">{{ number_format($mrp->pr_mb, 2) }} Kg</div>
                                <a href="{{ url('/ppic/create-pr') }}?no_mm={{ $mrp->mb_mm }}&qty={{ $mrp->pr_mb }}" style="background: #8b5cf6; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; display: inline-block;"><i class="fas fa-file-signature"></i> Buat PR</a>
                            @elseif(($mrp->status_mb ?? '') == 'On Order')
                                <span style="color: #d97706; font-size: 11px; font-weight: bold; background: #fef3c7; padding: 4px 8px; border-radius: 4px;"><i class="fas fa-clock"></i> Menunggu PR</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>

                        <!-- DATA BOX -->
                        <td style="padding: 12px; background: #f8fafc;">
                            <div style="font-size: 10px; color: #475569; white-space: normal; line-height: 1.2; margin-bottom: 4px;">{{ $mrp->nama_pack }}</div>
                            Stok: <b>{{ number_format($mrp->stok_pack ?? 0) }}</b> Pcs<br>
                            <span style="color: #475569; font-size: 10px; font-weight: bold;">Butuh: {{ number_format($mrp->kebutuhan_pack ?? 0) }} Pcs</span>
                        </td>
                        <td style="padding: 12px; text-align: center; background: #f8fafc;">
                            @if(($mrp->status_pack ?? '') == 'Shortage')
                                <span style="color: #dc2626; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> Shortage</span>
                            @elseif(($mrp->status_pack ?? '') == 'On Order')
                                <span style="color: #d97706; font-weight: bold;"><i class="fas fa-truck"></i> On Order</span>
                            @else
                                <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> Aman</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: right; border-right: 2px solid #94a3b8; background: #f8fafc;">
                            @if(($mrp->action_pack ?? 0) > 0)
                                <div style="color: #475569; font-weight: bold; margin-bottom: 5px;">Kurang: {{ number_format($mrp->action_pack) }} Pcs</div>
                                @if($mrp->is_csm)
                                    <button onclick="alert('PEMBERITAHUAN!\n\nBarang [{{ $mrp->nama_pack }}] adalah titipan Customer.\nSilakan Follow Up Sales!')" 
                                       style="background: #f59e0b; color: white; border: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; cursor: pointer; display: inline-block;">
                                       <i class="fas fa-phone-alt"></i> Follow Up
                                    </button>
                                @else
                                    <a href="{{ url('/ppic/create-pr') }}?no_mm={{ $mrp->pack_mm }}&qty={{ $mrp->action_pack }}" 
                                       style="background: #8b5cf6; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; display: inline-block;">
                                       <i class="fas fa-file-signature"></i> Buat PR
                                    </a>
                                @endif
                            @elseif(($mrp->status_pack ?? '') == 'On Order')
                                <span style="color: #d97706; font-size: 11px; font-weight: bold; background: #fef3c7; padding: 4px 8px; border-radius: 4px;"><i class="fas fa-clock"></i> Menunggu PR</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>

                        <!-- DATA GUSSET -->
                        <td style="padding: 12px; background: #fff1f2;">
                            <div style="font-size: 10px; color: #475569; white-space: normal; line-height: 1.2; margin-bottom: 4px;">{{ $mrp->nama_gusset }}</div>
                            Stok: <b>{{ number_format($mrp->stok_gusset ?? 0) }}</b> Pcs<br>
                            <span style="color: #be123c; font-size: 10px; font-weight: bold;">Butuh: {{ number_format($mrp->kebutuhan_gusset ?? 0) }} Pcs</span>
                        </td>
                        <td style="padding: 12px; text-align: center; background: #fff1f2;">
                            @if(($mrp->status_gusset ?? '') == 'Shortage')
                                <span style="color: #dc2626; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> Shortage</span>
                            @elseif(($mrp->status_gusset ?? '') == 'On Order')
                                <span style="color: #d97706; font-weight: bold;"><i class="fas fa-truck"></i> On Order</span>
                            @else
                                <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> Aman</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: right; background: #fff1f2;">
                            @if(($mrp->action_gusset ?? 0) > 0)
                                <div style="color: #be123c; font-weight: bold; margin-bottom: 5px;">Kurang: {{ number_format($mrp->action_gusset) }} Pcs</div>
                                @if($mrp->is_csm_gusset)
                                    <button onclick="alert('PEMBERITAHUAN!\n\nBarang [{{ $mrp->nama_gusset }}] adalah titipan Customer.\nSilakan Follow Up Sales!')" 
                                       style="background: #f59e0b; color: white; border: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; cursor: pointer; display: inline-block;">
                                       <i class="fas fa-phone-alt"></i> Follow Up
                                    </button>
                                @else
                                    <a href="{{ url('/ppic/create-pr') }}?no_mm={{ $mrp->gusset_mm }}&qty={{ $mrp->action_gusset }}" 
                                       style="background: #8b5cf6; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; display: inline-block;">
                                       <i class="fas fa-file-signature"></i> Buat PR
                                    </a>
                                @endif
                            @elseif(($mrp->status_gusset ?? '') == 'On Order')
                                <span style="color: #d97706; font-size: 11px; font-weight: bold; background: #fef3c7; padding: 4px 8px; border-radius: 4px;"><i class="fas fa-clock"></i> Menunggu PR</span>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>

                    @else
                        <!-- DATA COVERAGE -->
                        <td colspan="21" style="padding: 15px; text-align: center; background: #f8fafc;">
                            <div style="display: flex; justify-content: space-around; align-items: center;">
                                <div>Total Stok (FG + Pweb Yield): <b style="color: #0ea5e9;">{{ number_format($mrp->stok_fg + ($mrp->stok_pweb * 0.85)) }}</b></div>
                                <div style="color: #64748b;">{{ $mrp->ket_coverage }}</div>
                                <div>
                                    @if($mrp->status_coverage == 'Shortage')
                                        <span style="background: #fef2f2; color: #dc2626; padding: 4px 8px; border-radius: 4px; font-weight: bold; margin-right: 10px;">Shortage</span>
                                        <a href="{{ url('/ppic/create-pr') }}?no_mm={{ $mrp->pweb_mm ?? $mrp->no_mm }}&qty={{ $mrp->kebutuhan_coverage }}" style="background: #8b5cf6; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; display: inline-block; box-shadow: 0 2px 4px rgba(139,92,246,0.2);"><i class="fas fa-file-signature"></i> Buat PR: {{ number_format($mrp->kebutuhan_coverage) }} Pcs</a>
                                    @else
                                        <span style="background: #f0fdf4; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: bold;">Stok Aman Coverage</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="23" style="text-align: center; padding: 40px; color: #94a3b8; font-style: italic;">Belum ada data Forecast aktif untuk dikalkulasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection