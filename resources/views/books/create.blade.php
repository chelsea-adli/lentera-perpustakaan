@extends('layouts.app')

@section('title', 'Tambah Buku | LENTERA')

@section('content')
<div class="container add-book-page">

    {{-- HEADER --}}
    <div class="add-book-header">
        <div>
            <span class="header-icon">📖</span>
            <div>
                <h1>Tambah Buku</h1>
                <p>Lengkapi informasi buku yang akan ditambahkan ke perpustakaan.</p>
            </div>
        </div>

        <div class="header-decoration">
            <span>“ Buku hari ini,</span>
            <span>ilmu untuk nanti ” 📚</span>
        </div>
    </div>

    {{-- PESAN ERROR --}}
    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('books.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="add-book-card">

            {{-- FORM KIRI --}}
            <div class="book-form">

                <div class="form-grid">

                    <div class="form-group">
                        <label for="title">📚 Judul Buku <span>*</span></label>
                        <input type="text" id="title" name="title"
                               value="{{ old('title') }}"
                               placeholder="Masukkan judul buku..." required>
                    </div>

                    <div class="form-group">
                        <label for="author">✍️ Penulis <span>*</span></label>
                        <input type="text" id="author" name="author"
                               value="{{ old('author') }}"
                               placeholder="Masukkan nama penulis..." required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">▦ Kategori <span>*</span></label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Pilih kategori...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="publisher">🏢 Penerbit</label>
                        <input type="text" id="publisher" name="publisher"
                               value="{{ old('publisher') }}"
                               placeholder="Masukkan nama penerbit...">
                    </div>

                    <div class="form-group">
                        <label for="publication_year">📅 Tahun Terbit</label>
                        <input type="number" id="publication_year"
                               name="publication_year"
                               min="1000" max="2100"
                               value="{{ old('publication_year') }}"
                               placeholder="Contoh: 2020">
                    </div>

                    <div class="form-group">
                        <label for="isbn">🔢 ISBN (Opsional)</label>
                        <input type="text" id="isbn" name="isbn"
                               value="{{ old('isbn') }}"
                               placeholder="Masukkan nomor ISBN...">
                    </div>

                    <div class="form-group">
                        <label for="stock">📦 Jumlah Stok <span>*</span></label>
                        <input type="number" id="stock" name="stock"
                               min="0" value="{{ old('stock', 1) }}" required>
                    </div>

                    {{-- UPLOAD GAMBAR --}}
                    <div class="form-group">
                        <label for="cover">🖼️ Gambar Sampul Buku</label>

                        <label for="cover" class="upload-area">
                            <div class="upload-icon">☁️</div>
                            <strong>Klik untuk mengunggah gambar</strong>
                            <small>atau pilih gambar dari laptop</small>
                            <small class="upload-format">JPG, JPEG, PNG, WEBP • Maksimal 2 MB</small>
                        </label>

                        <input type="file" id="cover" name="cover"
                               accept=".jpg,.jpeg,.png,.webp,image/*"
                               hidden>

                        <div id="file-name" class="file-name">
                            Belum ada gambar dipilih
                        </div>
                    </div>

                </div>

                {{-- DESKRIPSI --}}
                <div class="form-group description-group">
                    <label for="description">📄 Deskripsi Buku</label>
                    <textarea id="description" name="description"
                              rows="4" maxlength="1000"
                              placeholder="Masukkan deskripsi buku...">{{ old('description') }}</textarea>
                    <small id="char-count">0/1000 karakter</small>
                </div>

            </div>

            {{-- PREVIEW KANAN --}}
            <div class="preview-panel">
                <div class="preview-heading">
                    <span>👁️</span>
                    Preview Gambar
                </div>

                <div class="preview-image">
                    <div id="preview-placeholder">
                        <span>📚</span>
                        <strong>LENTERA</strong>
                        <small>Sampul buku akan muncul di sini</small>
                    </div>

                    <img id="cover-preview" src="" alt="Preview sampul buku"
                         style="display:none">
                </div>

                <div class="preview-info">
                    <span>ℹ️</span>
                    <p>Gambar sampul akan ditampilkan di katalog buku setelah buku berhasil ditambahkan.</p>
                </div>
            </div>

            {{-- TOMBOL --}}
            <div class="form-actions">
                <a href="{{ route('books.index') }}" class="back-button">
                    ← Kembali
                </a>

                <button type="submit" class="save-button">
                    💾 Simpan Buku
                </button>
            </div>

        </div>
    </form>

</div>

<style>
.add-book-page {
    color: #1c2945;
}

.add-book-header {
    background: linear-gradient(120deg, #e7edff, #f4f7ff);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.add-book-header > div:first-child {
    display: flex;
    align-items: center;
    gap: 16px;
}

.header-icon {
    font-size: 42px;
}

.add-book-header h1 {
    margin: 0 0 6px;
    font-size: 27px;
    color: #172b61;
}

.add-book-header p {
    margin: 0;
    color: #52658f;
    font-size: 13px;
}

.header-decoration {
    display: flex;
    flex-direction: column;
    text-align: right;
    color: #3659d9;
    font-weight: 700;
    font-style: italic;
    line-height: 1.6;
}

.add-book-card {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(280px, .95fr);
    gap: 25px;
    padding: 25px;
    background: white;
    border: 1px solid #e1e8fa;
    border-radius: 17px;
    box-shadow: 0 5px 20px rgba(31, 65, 130, .06);
}

.book-form {
    min-width: 0;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 13px;
    font-weight: 700;
    color: #172b61;
}

.form-group label span {
    color: #e33445;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 12px 14px;
    border: 1px solid #cbd5e7;
    border-radius: 9px;
    background: #fff;
    color: #1c2945;
    font: inherit;
    font-size: 13px;
    outline: none;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #3659d9;
    box-shadow: 0 0 0 3px rgba(54, 89, 217, .10);
}

.upload-area {
    min-height: 115px;
    border: 2px dashed #7894ef;
    background: #f7f9ff;
    border-radius: 11px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    text-align: center;
    padding: 12px;
}

.upload-area:hover {
    background: #edf2ff;
}

.upload-icon {
    font-size: 28px;
    margin-bottom: 5px;
}

.upload-area strong {
    color: #3659d9;
    font-size: 13px;
}

.upload-area small {
    color: #64748b;
    font-size: 11px;
    margin-top: 4px;
}

.upload-area .upload-format {
    background: #e3ebff;
    color: #3659d9;
    padding: 5px 10px;
    border-radius: 20px;
}

.file-name {
    font-size: 11px;
    color: #64748b;
    margin-top: 5px;
    overflow-wrap: anywhere;
}

.description-group {
    margin-top: 22px;
    position: relative;
}

.description-group textarea {
    resize: vertical;
    min-height: 110px;
}

#char-count {
    text-align: right;
    font-size: 11px;
    color: #64748b;
}

.preview-panel {
    border: 1px solid #dce5fa;
    border-radius: 15px;
    padding: 15px;
    align-self: start;
}

.preview-heading {
    background: #e8eeff;
    color: #172b61;
    padding: 13px;
    border-radius: 9px;
    font-weight: 800;
    font-size: 14px;
    margin-bottom: 15px;
}

.preview-image {
    height: 300px;
    border-radius: 12px;
    background: #f0f4ff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.preview-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

#preview-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #3659d9;
    gap: 8px;
    text-align: center;
    padding: 15px;
}

#preview-placeholder span {
    font-size: 75px;
}

#preview-placeholder small {
    color: #64748b;
    font-size: 11px;
}

.preview-info {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    background: #edf3ff;
    border: 1px solid #d4e0ff;
    border-radius: 9px;
    padding: 12px;
    margin-top: 13px;
}

.preview-info p {
    margin: 0;
    color: #344d86;
    font-size: 11px;
    line-height: 1.6;
}

.form-actions {
    grid-column: 1 / -1;
    border-top: 1px solid #e2e8f0;
    padding-top: 18px;
    display: flex;
    justify-content: space-between;
    gap: 12px;
}

.back-button,
.save-button {
    padding: 12px 20px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

.back-button {
    border: 1px solid #3659d9;
    color: #3659d9;
    background: white;
}

.save-button {
    border: none;
    color: white;
    background: #3659d9;
    min-width: 190px;
}

.save-button:hover {
    background: #2444b5;
}

.error-box {
    background: #fee2e2;
    color: #b91c1c;
    padding: 13px 17px;
    border-radius: 9px;
    margin-bottom: 18px;
    font-size: 13px;
}

@media (max-width: 850px) {
    .add-book-card {
        grid-template-columns: 1fr;
    }

    .preview-panel {
        grid-row: auto;
    }
}

@media (max-width: 600px) {
    .form-grid {
        grid-template-columns: 1fr;
    }

    .add-book-header {
        padding: 20px;
    }

    .header-decoration {
        display: none;
    }

    .add-book-card {
        padding: 16px;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .back-button,
    .save-button {
        text-align: center;
        width: 100%;
        box-sizing: border-box;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const coverInput = document.getElementById('cover');
    const coverPreview = document.getElementById('cover-preview');
    const placeholder = document.getElementById('preview-placeholder');
    const fileName = document.getElementById('file-name');
    const description = document.getElementById('description');
    const charCount = document.getElementById('char-count');

    coverInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            coverPreview.style.display = 'none';
            placeholder.style.display = 'flex';
            fileName.textContent = 'Belum ada gambar dipilih';
            return;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!allowedTypes.includes(file.type)) {
            alert('Format gambar harus JPG, PNG, atau WEBP.');
            this.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran gambar maksimal 2 MB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            coverPreview.src = event.target.result;
            coverPreview.style.display = 'block';
            placeholder.style.display = 'none';
        };

        reader.readAsDataURL(file);
        fileName.textContent = file.name;
    });

    function updateCharCount() {
        charCount.textContent = description.value.length + '/1000 karakter';
    }

    description.addEventListener('input', updateCharCount);
    updateCharCount();
});
</script>

@endsection
