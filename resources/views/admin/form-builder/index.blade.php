@extends('admin.layouts.app')

@section('title', 'Form Builder - Admin Buku Tamu')

@section('styles')
<style>
    .form-builder-container {
        max-width: 1200px;
        margin: 0 auto;
        animation: fadeInUp 0.6s ease-out;
    }

    .field-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .field-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        border-color: var(--custom-primary);
    }

    .field-handle {
        cursor: grab;
        color: #94a3b8;
        font-size: 20px;
    }

    .field-info {
        flex-grow: 1;
    }

    .field-label {
        font-weight: 700;
        color: var(--custom-text);
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .field-type-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 20px;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }

    .field-placeholder {
        font-size: 13px;
        color: #64748b;
    }

    .field-actions {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: none;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-edit { background: #f0fdf4; color: #166534; }
    .btn-edit:hover { background: #dcfce7; }
    .btn-delete { background: #fef2f2; color: #991b1b; }
    .btn-delete:hover { background: #fee2e2; }

    .modal-content {
        border-radius: 24px;
        border: none;
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--custom-primary), var(--custom-secondary));
        color: white;
        border: none;
        padding: 24px;
    }

    .modal-title { font-weight: 700; }

    .modal-body { padding: 32px; }

    .option-row {
        display: flex;
        gap: 8px;
        margin-bottom: 8px;
    }

    html.theme-dark .field-card {
        background: #1e1e1e;
        border-color: #333;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    html.theme-dark .field-label {
        color: #f8fafc;
    }

    html.theme-dark .field-placeholder {
        color: #94a3b8;
    }

    html.theme-dark .field-type-badge {
        background: #334155;
        color: #cbd5e1;
    }

    /* Override inline styles for core badge in dark mode */
    html.theme-dark .field-type-badge[style*="background: #e0f2fe"] {
        background: #0c4a6e !important;
        color: #7dd3fc !important;
    }

    /* Override inline styles for hidden badge in dark mode */
    html.theme-dark .field-type-badge[style*="background: #fee2e2"] {
        background: #7f1d1d !important;
        color: #fca5a5 !important;
    }

    html.theme-dark .btn-edit { background: #064e3b; color: #34d399; }
    html.theme-dark .btn-edit:hover { background: #065f46; }
    html.theme-dark .btn-delete { background: #7f1d1d; color: #f87171; }
    html.theme-dark .btn-delete:hover { background: #991b1b; }

    /* Modal Dark Mode Fixes */
    html.theme-dark .modal-content {
        background: #1e1e1e;
        color: #f1f5f9;
        border: 1px solid #333;
    }

    html.theme-dark .modal-body {
        background: #1e1e1e;
    }

    html.theme-dark .form-control {
        background: #2a2a2a;
        border-color: #444;
        color: #f1f5f9;
    }

    html.theme-dark .form-control:focus {
        background: #2a2a2a;
        border-color: var(--custom-primary);
        color: #fff;
    }

    html.theme-dark .form-label {
        color: #cbd5e1;
    }

    html.theme-dark .modal-footer {
        background: #1e1e1e;
        border-top: 1px solid #333;
    }

    html.theme-dark .btn-light {
        background: #333;
        color: #f1f5f9;
        border: none;
    }

    html.theme-dark .btn-light:hover {
        background: #444;
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="form-builder-container">
    <header class="main-header" style="margin-bottom: 32px;">
        <div class="main-header-title">
            <h1>Kustomisasi Form Tamu</h1>
            <p>Atur kolom apa saja yang harus diisi oleh tamu Anda</p>
        </div>
        <button class="btn-primary" style="width: auto;" data-bs-toggle="modal" data-bs-target="#addFieldModal">
            <i class="bi bi-plus-lg"></i> Tambah Kolom Baru
        </button>
    </header>

    @if(session('success'))
        <div class="success-message">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div id="fieldsList">
        @foreach($fields as $field)
            <div class="field-card" data-id="{{ $field->id }}">
                <div class="field-handle"><i class="bi bi-grip-vertical"></i></div>
                <div class="field-info">
                    <div class="field-label">
                        {{ $field->label }}
                        @if($field->is_required)
                            <span style="color: #ef4444;">*</span>
                        @endif
                        <span class="field-type-badge">{{ $field->type }}</span>
                        @if($field->is_core)
                            <span class="field-type-badge" style="background: #e0f2fe; color: #0369a1;">Inti</span>
                        @endif
                        @if(!$field->is_visible)
                            <span class="field-type-badge" style="background: #fee2e2; color: #991b1b;">Tersembunyi</span>
                        @endif
                    </div>
                    <div class="field-placeholder">
                        {{ $field->placeholder ?: 'Tanpa placeholder' }}
                    </div>
                </div>
                <div class="field-actions">
                    <button class="btn-icon btn-edit" onclick="editField({{ $field }})">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    @if(!$field->is_core)
                        <form action="{{ route('admin.form-builder.destroy', $field) }}" method="POST" onsubmit="return confirm('Hapus kolom ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon btn-delete">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Add/Edit Field Modal -->
<div class="modal fade" id="addFieldModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="fieldForm" action="{{ route('admin.form-builder.store') }}" method="POST" class="modal-content">
            @csrf
            <div id="methodField"></div>
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Kolom Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Kolom (Unik, huruf kecil, tanpa spasi)</label>
                        <input type="text" name="name" id="fieldName" class="form-control" placeholder="misal: tujuan_kunjungan" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Label Kolom</label>
                        <input type="text" name="label" id="fieldLabel" class="form-control" placeholder="misal: Apa tujuan kunjungan Anda?" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Placeholder</label>
                        <input type="text" name="placeholder" id="fieldPlaceholder" class="form-control" placeholder="Teks bantuan di dalam input">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe Input</label>
                        <select name="type" id="fieldType" class="form-control" onchange="toggleOptions()">
                            <option value="text">Teks Pendek</option>
                            <option value="textarea">Teks Panjang</option>
                            <option value="number">Angka</option>
                            <option value="email">Email</option>
                            <option value="tel">No. Telepon</option>
                            <option value="select">Pilihan (Dropdown)</option>
                            <option value="radio">Pilihan Tunggal (Radio)</option>
                            <option value="checkbox">Pilihan Banyak (Checkbox)</option>
                            <option value="date">Tanggal</option>
                            <option value="time">Waktu</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3" id="optionsContainer" style="display: none;">
                        <label class="form-label">Pilihan (Options)</label>
                        <div id="optionsList">
                            <!-- Options will be added here -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addOption()">
                            <i class="bi bi-plus"></i> Tambah Pilihan
                        </button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_required" id="fieldRequired" value="1">
                            <label class="form-check-label" for="fieldRequired">Wajib diisi (Required)</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_visible" id="fieldVisible" value="1" checked>
                            <label class="form-check-label" for="fieldVisible">Tampilkan di Form</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn-primary" style="width: auto;">Simpan Kolom</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const fieldsList = document.getElementById('fieldsList');
    new Sortable(fieldsList, {
        handle: '.field-handle',
        animation: 150,
        onEnd: function() {
            const orders = [];
            fieldsList.querySelectorAll('.field-card').forEach((el, index) => {
                orders.push({ id: el.dataset.id, order: index + 1 });
            });
            
            fetch('{{ route("admin.form-builder.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ orders })
            });
        }
    });

    function toggleOptions() {
        const type = document.getElementById('fieldType').value;
        const container = document.getElementById('optionsContainer');
        container.style.display = ['select', 'radio', 'checkbox'].includes(type) ? 'block' : 'none';
    }

    function addOption(key = '', value = '') {
        const div = document.createElement('div');
        div.className = 'option-row';
        div.innerHTML = `
            <input type="text" name="options[${key}]" class="form-control" placeholder="Nilai" value="${value}" required>
            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        `;
        document.getElementById('optionsList').appendChild(div);
    }

    function editField(field) {
        document.getElementById('modalTitle').innerText = 'Edit Kolom';
        document.getElementById('fieldForm').action = `{{ url('admin/form-builder') }}/${field.id}`;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        
        document.getElementById('fieldName').value = field.name;
        document.getElementById('fieldName').disabled = true; // Cannot change name for core or existing
        document.getElementById('fieldLabel').value = field.label;
        document.getElementById('fieldPlaceholder').value = field.placeholder || '';
        document.getElementById('fieldType').value = field.type;
        document.getElementById('fieldRequired').checked = field.is_required;
        document.getElementById('fieldVisible').checked = field.is_visible;
        
        if (field.is_core) {
            document.getElementById('fieldType').disabled = true;
        } else {
            document.getElementById('fieldType').disabled = false;
        }

        document.getElementById('optionsList').innerHTML = '';
        if (field.options) {
            Object.entries(field.options).forEach(([k, v]) => addOption(k, v));
        }
        
        toggleOptions();
        new bootstrap.Modal(document.getElementById('addFieldModal')).show();
    }

    // Reset form when modal is hidden
    document.getElementById('addFieldModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('fieldForm').reset();
        document.getElementById('fieldForm').action = "{{ route('admin.form-builder.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('modalTitle').innerText = 'Tambah Kolom Baru';
        document.getElementById('fieldName').disabled = false;
        document.getElementById('fieldType').disabled = false;
        document.getElementById('optionsList').innerHTML = '';
        toggleOptions();
    });
</script>
@endsection
