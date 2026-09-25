@extends('layouts.staff-layout')

@push('styles')
<style>
    .modern-container {
        padding: 20px 30px;
        background-color: #f4f7f6;
        min-height: 100vh;
    }

    .modern-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.04);
        padding: 30px;
        border: 1px solid #edf2f7;
    }

    .modern-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 25px;
        border-bottom: 2px solid #edf2f7;
        padding-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .form-group {
        margin-bottom: 0; 
    }
    
    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
        display: block;
        font-size: 0.9rem;
    }

    .modern-input, .normal-dropdown {
        width: 100% !important;
        box-sizing: border-box !important;
        padding: 10px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        color: #2d3748;
        background-color: #f8fafc;
        transition: all 0.3s ease;
        height: 42px;
    }
    
    .modern-input:focus, .normal-dropdown:focus {
        border-color: #3182ce;
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
        outline: none;
        background-color: #ffffff;
    }

    .form-row-split {
        display: flex;
        gap: 20px; 
        margin-bottom: 1.25rem;
        width: 100%;
    }
    
    .form-col-split {
        flex: 1;
        min-width: 0; 
    }

    .table-wrapper {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        overflow-x: auto;
        margin-top: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }
    .modern-table thead {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .modern-table th {
        padding: 12px 15px;
        font-weight: 700;
        color: #4a5568;
        font-size: 0.85rem;
        text-align: left;
        white-space: nowrap; 
    }
    .modern-table td {
        padding: 12px 15px;
        border-bottom: 2px solid #edf2f7;
        vertical-align: middle;
    }
    .modern-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .modern-table input, .modern-table select {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px; 
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.9rem;
        background-color: #f8fafc;
        transition: all 0.2s;
        height: 40px; 
    }

    .modern-table td:nth-child(1) input { min-width: 130px; } /* PIC */
    .modern-table td:nth-child(2) input { min-width: 100px; } /* No. Box */
    .modern-table td:nth-child(3) select, 
    .modern-table td:nth-child(3) input { min-width: 180px; } /* Defect */
    .modern-table td:nth-child(4) input, 
    .modern-table td:nth-child(5) input, 
    .modern-table td:nth-child(6) input { min-width: 80px; }  /* Kolom AQL */

    .modern-table input:focus, .modern-table select:focus {
        border-color: #3182ce;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
        outline: none;
    }

    .modern-btn-primary {
        background-color: #3182ce;
        color: white;
        padding: 10px 24px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: background-color 0.3s;
        cursor: pointer;
    }
    .modern-btn-primary:hover {
        background-color: #2b6cb0;
    }

    .modern-btn-success {
        background-color: #38a169;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .modern-btn-success:hover {
        background-color: #2f855a;
    }

    .modern-btn-danger {
        background-color: #e53e3e;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .modern-btn-danger:hover {
        background-color: #c53030;
    }
</style>
@endpush

@section('konten')
<div class="modern-container">
    <form action="{{ url('/in-proses/fg/store') }}" method="POST">
        @csrf
        
        <div class="modern-card">
            <!-- Tombol Kembali dipindah ke dalam card agar sejajar -->
            <div style="margin-bottom: 20px;">
                <a href="{{ url('/in-proses/fg/menu') }}" class="btn-kembali" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; background-color: #64748b; color: white; padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: bold; transition: background 0.2s;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Menu
                </a>
            </div>

            <!-- Judul Form -->
            <div class="modern-card-title">
                <i class="fas fa-file-signature text-primary" style="margin-right: 10px;"></i> 
                Input Finish Good (FG) Baru (Sistem Dinamis)
            </div>

            <!-- BARIS 1 -->
            <div class="form-row-split">
                <div class="form-col-split">
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="modern-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="form-col-split">
                    <div class="form-group">
                        <label class="form-label">Shift Kerja</label>
                        <select name="shift" class="normal-dropdown" required>
                            <option value="Shift 1">Shift 1</option>
                            <option value="Shift 2">Shift 2</option>
                            <option value="Shift 3">Shift 3</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- BARIS 2 -->
            <div class="form-row-split">
                <div class="form-col-split">
                    <div class="form-group">
                        <label class="form-label">No. Batch</label>
                        <input type="text" name="no_batch" class="modern-input" placeholder="Masukkan No Batch..." required>
                    </div>
                </div>
                <div class="form-col-split">
                    <div class="form-group">
                        <label class="form-label">Line Produksi</label>
                        <select name="line_produksi" class="normal-dropdown" required>
                            <option value="">-- Pilih Line --</option>
                            <option value="Line 1">Line 1</option>
                            <option value="Line 2">Line 2</option>
                            <option value="Line 3">Line 3</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- BARIS TAMBAHAN KHUSUS TIPE INSPEKSI -->
            <div class="form-row-split">
                <div class="form-col-split" style="width: 100%;">
                    <div class="form-group">
                        <label class="form-label">Tipe Inspeksi</label>
                        <select name="inspection_type" class="normal-dropdown" required>
                            <option value="Normal Inspection">Normal Inspection (Inspeksi Normal)</option>
                            <option value="Tightened Inspection">Tightened Inspection (Inspeksi Ketat)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- BARIS 3: Customer & No. MM / Item Produk -->
            <div class="form-row-split">
                <div class="form-col-split">
                    <div class="form-group">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" id="customer_id" class="normal-dropdown" required>
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->nama_customer }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-col-split">
                    <div class="form-group">
                        <label class="form-label">No. MM / Item Produk</label>
                        <select name="no_mm" id="no_mm" class="normal-dropdown" required>
                            <option value="">-- Pilih Item Produk --</option>
                            @foreach($masterItems as $item)
                                <option value="{{ $item->no_mm }}">{{ $item->no_mm }} - {{ $item->nama_material ?? $item->nama_item }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
           
            <!-- KOTAK PLACEHOLDER -->
            <div id="placeholder-box" style="background-color: #f8fafc; border: 1px dashed #cbd5e0; border-radius: 8px; padding: 25px; text-align: center; margin-bottom: 20px; margin-top: 10px;">
                <span style="color: #718096; font-style: italic; font-size: 14px;">
                    <i class="fas fa-info-circle mr-1"></i> Pilih Customer dan Item Produk di atas untuk memunculkan parameter pengecekan...
                </span>
            </div>

            <!-- WRAPPER TABEL INSPEKSI -->
            <div id="wrapper-inspection-table" style="display: none;">
                
                <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                    <button type="button" id="btn-tambah-baris" class="modern-btn-success">
                        <i class="fas fa-plus mr-1"></i> Tambah Baris
                    </button>
                </div>

                <div class="table-wrapper mb-4">
                    <table class="modern-table" id="table-inspection-detail">
                        <thead>
                            <tr id="dynamic-header-row">
                                <!-- Header kolom dirender otomatis -->
                            </tr>
                        </thead>
                        <tbody id="dynamic-row-container">
                            <!-- Baris input dirender otomatis -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- GARIS PEMISAH & TOMBOL SIMPAN -->
            <hr style="border-top: 1px solid #e2e8f0; margin: 30px 0 20px 0;">
            
            <div style="text-align: right;">
                <button type="submit" class="modern-btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Dokumen FG
                </button>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const masterDefects = @json($defects ?? []);

    $(document).ready(function() {
        let activeAqlLabels = [];

        $('#customer_id').on('change', function() {
            let customer_id = $(this).val();
            if (!customer_id) {
                $('#wrapper-inspection-table').hide();
                $('#placeholder-box').show();
                $('#dynamic-row-container').html('');
                activeAqlLabels = [];
                return;
            }

            $('#wrapper-inspection-table').show();
            $('#placeholder-box').hide();

            $.ajax({
                url: "{{ url('/in-proses/fg/get-defect-categories') }}/" + customer_id,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if(response.status === 'success') {
                        activeAqlLabels = response.labels;
                        buildTableStructure(activeAqlLabels);
                        $('#dynamic-row-container').html('');
                        addRowFG();
                    }
                },
                error: function(xhr) {
                    console.error("Gagal mengambil data kategori defect:", xhr.responseText);
                    alert("Terjadi kesalahan saat memuat parameter pengecekan.");
                }
            });
        });

        function buildTableStructure(labels) {
            let headerHtml = `
                <th>PIC</th>
                <th>No. Box</th>
                <th>Defect</th>
            `;

            labels.forEach(function(label, index) {
                headerHtml += `
                    <th>
                        ${label}
                        <input type="hidden" name="aql_labels[]" value="${label}">
                    </th>
                `;
            });

            headerHtml += `
                <th>Decision</th>
                <th>Jml Box</th>
                <th>Sortir OK</th>
                <th>Sortir NG</th>
                <th style="text-align:center;">Aksi</th>
            `;

            $('#dynamic-header-row').html(headerHtml);
        }

        $('#btn-tambah-baris').click(function() {
            addRowFG();
        });

        function addRowFG() {
            if (activeAqlLabels.length === 0) return;

            let defectOptionsHtml = '<option value="">-- Pilih Defect --</option>';
            masterDefects.forEach(function(def) {
                defectOptionsHtml += `<option value="${def.nama_defect}">${def.nama_defect}</option>`;
            });

            let trHtml = `<tr>
                <td><input type="text" name="pic[]" placeholder="Nama PIC" required></td>
                <td><input type="text" name="no_box[]" placeholder="No. Box"></td>
                <td>
                    <select name="defect[]" required>
                        ${defectOptionsHtml}
                    </select>
                </td>`;

            activeAqlLabels.forEach(function(label, index) {
                trHtml += `<td><input type="number" step="any" name="aql_col_${index}[]" value="0" min="0"></td>`;
            });

            trHtml += `
                <td>
                    <select name="decision[]">
                        <option value="OK">OK</option>
                        <option value="NG">NG</option>
                        <option value="SORTIR">SORTIR</option>
                    </select>
                </td>
                <td><input type="number" name="jml_box[]" value="0" min="0"></td>
                <td><input type="number" name="sortir_ok[]" value="0" min="0"></td>
                <td><input type="number" name="sortir_ng[]" value="0" min="0"></td>
                <td style="text-align: center;">
                    <button type="button" class="modern-btn-danger btn-hapus-fg">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;

            $('#dynamic-row-container').append(trHtml);
        }

        $(document).on('click', '.btn-hapus-fg', function() {$(this).closest('tr').remove();
        });
    });
</script>
@endpush