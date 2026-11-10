@extends('layouts.app')

@section('title', 'Edit Buku | LENTERA')

@section('content')

<div class="edit-book-page">

    <div class="edit-header">
        <div>
            <div class="edit-breadcrumb">
                KATALOG BUKU <span>/</span> EDIT BUKU
            </div>

            <h1>Edit Informasi Buku</h1>
            <p>Perbarui informasi dan sampul buku yang ada di perpustakaan LENTERA.</p>
        </div>

        <a href="{{ route('books.index') }}" class="back-button">
            ← Kembali ke Katalog
        </a>
    </div>

    @if ($errors->any())
        <div class="edit-error">
            <strong>Periksa kembali data yang dimasukkan.</strong>

            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form
        action="{{ route('books.update', $book) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="edit-layout">

            {{-- INFORMASI BUKU --}}
            <div class="edit-main-card">

                <div class="section-heading">
                    <div class="section-icon">📖</div>
                    <div>
                        <h2>Informasi Buku</h2>
                        <p>Lengkapi informasi buku dengan benar.</p>
                    </div>
                </div>

                <div class="form-grid">

                    <div class="form-group full-width">
                        <label for="title">Judul Buku <span>*</span></label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title', $book->title) }}"
                            placeholder="Masukkan judul buku"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="author">Penulis <span>*</span></label>
                        <input
                            type="text"
                            id="author"
                            name="author"
                            value="{{ old('author', $book->author) }}"
                            placeholder="Nama penulis"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="category_id">Kategori <span>*</span></label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Pilih kategori</option>

                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(old('category_id', $book->category_id) == $category->id)
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="publisher">Penerbit</label>
                        <input
                            type="text"
                            id="publisher"
                            name="publisher"
                            value="{{ old('publisher', $book->publisher) }}"
                            placeholder="Nama penerbit"
                        >
                    </div>

                    <div class="form-group">
                        <label for="publication_year">Tahun Terbit</label>
                        <input
                            type="number"
                            id="publication_year"
                            name="publication_year"
                            min="1000"
                            max="2100"
                            value="{{ old('publication_year', $book->publication_year) }}"
                            placeholder="Contoh: 2024"
                        >
                    </div>

                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input
                            type="text"
                            id="isbn"
                            name="isbn"
                            value="{{ old('isbn', $book->isbn) }}"
                            placeholder="Nomor ISBN buku"
                        >
                    </div>

                    <div class="form-group">
                        <label for="stock">Jumlah Stok <span>*</span></label>
                        <input
                            type="number"
                            id="stock"
                            name="stock"
                            min="0"
                            value="{{ old('stock', $book->stock) }}"
                            placeholder="Jumlah buku"
                            required
                        >
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Deskripsi Buku</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Tuliskan deskripsi atau ringkasan buku..."
                        >{{ old('description', $book->description) }}</textarea>
                    </div>

                </div>
            </div>

            {{-- SAMPUL BUKU --}}
            <div class="edit-side-card">

                <div class="section-heading">
                    <div class="section-icon">🖼️</div>
                    <div>
                        <h2>Sampul Buku</h2>
                        <p>Ubah gambar sampul buku.</p>
                    </div>
                </div>

                <div class="cover-preview">

                    @if ($book->cover)
                        <img
                            id="coverPreview"
                            src="{{ asset('storage/' . $book->cover) }}"
                            alt="Sampul {{ $book->title }}"
                        >

                        <div id="coverPlaceholder" class="cover-placeholder" hidden>
                            <span>📚</span>
                            <p>Belum ada sampul buku</p>
                        </div>
                    @else
                        <img
                            id="coverPreview"
                            src=""
                            alt="Preview sampul buku"
                            hidden
                        >

                        <div id="coverPlaceholder" class="cover-placeholder">
                            <span>📚</span>
                            <p>Belum ada sampul buku</p>
                        </div>
                    @endif

                </div>

                <label for="cover" class="upload-label">
                    <span>📁</span>
                    Pilih Gambar Baru
                </label>

                <input
                    type="file"
                    id="cover"
                    name="cover"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="cover-input"
                >

                <p class="upload-info">
                    Format JPG, JPEG, PNG, atau WEBP.
                    Maksimal ukuran file 2 MB.
                </p>

                <div class="cover-note">
                    <strong>Catatan:</strong>
                    Kosongkan pilihan gambar jika ingin tetap menggunakan sampul lama.
                </div>

            </div>

        </div>

        {{-- TOMBOL AKSI --}}
        <div class="edit-actions">
            <a href="{{ route('books.index') }}" class="cancel-button">
                Batal
            </a>

            <button type="submit" class="save-button">
                ✓ Simpan Perubahan
            </button>
        </div>

    </form>

</div>

<style>
    .edit-book-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 25px 10px 45px;
        color: #1c2945;
    }

    .edit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .edit-breadcrumb {
        color: #3659d9;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 9px;
    }

    .edit-breadcrumb span {
        color: #94a3b8;
        margin: 0 6px;
    }

    .edit-header h1 {
        font-size: 27px;
        margin: 0 0 7px;
        color: #1c2945;
    }

    .edit-header p {
        color: #718096;
        font-size: 13px;
        margin: 0;
    }

    .back-button {
        padding: 11px 16px;
        border: 1px solid #dce3f0;
        border-radius: 9px;
        color: #3659d9;
        background: white;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
    }

    .back-button:hover {
        background: #f0f4ff;
    }

    .edit-error {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
        padding: 15px 18px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 20px;
        line-height: 1.8;
    }

    .edit-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 310px;
        gap: 22px;
        align-items: start;
    }

    .edit-main-card,
    .edit-side-card {
        background: white;
        border: 1px solid #e5eaf3;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(28, 41, 69, .04);
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 20px;
        margin-bottom: 22px;
        border-bottom: 1px solid #edf0f5;
    }

    .section-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #edf2ff;
        border-radius: 11px;
        font-size: 20px;
    }

    .section-heading h2 {
        font-size: 16px;
        margin: 0 0 5px;
        color: #1c2945;
    }

    .section-heading p {
        color: #8994a7;
        font-size: 11px;
        margin: 0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 19px;
    }

    .form-group {
        min-width: 0;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #34415c;
        margin-bottom: 8px;
    }

    .form-group label span {
        color: #e05252;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        display: block;
        width: 100%;
        box-sizing: border-box;
        padding: 12px 13px;
        border: 1px solid #dce3ee;
        border-radius: 9px;
        background: #fff;
        color: #26344f;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: .2s;
    }

    .form-group input,
    .form-group select {
        min-height: 44px;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 125px;
        line-height: 1.6;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #3659d9;
        box-shadow: 0 0 0 3px rgba(54, 89, 217, .10);
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #a0aabd;
    }

    .cover-preview {
        width: 100%;
        height: 310px;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8faff;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }

    .cover-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
        box-sizing: border-box;
    }

    .cover-preview img[hidden],
    .cover-placeholder[hidden] {
        display: none;
    }

    .cover-placeholder {
        text-align: center;
        color: #94a3b8;
    }

    .cover-placeholder span {
        font-size: 55px;
    }

    .cover-placeholder p {
        font-size: 12px;
    }

    .upload-label {
        display: block;
        text-align: center;
        padding: 12px;
        border-radius: 9px;
        background: #edf2ff;
        color: #3659d9;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: .2s;
    }

    .upload-label:hover {
        background: #dfe7ff;
    }

    .upload-label span {
        margin-right: 5px;
    }

    .cover-input {
        display: none;
    }

    .upload-info {
        font-size: 11px;
        color: #8994a7;
        line-height: 1.7;
        margin-top: 12px;
    }

    .cover-note {
        background: #f5f7fc;
        border-radius: 9px;
        padding: 12px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.7;
        margin-top: 16px;
    }

    .cover-note strong {
        color: #3659d9;
    }

    .edit-actions {
        display: flex;
        justify-content: flex-end;
        gap: 11px;
        margin-top: 22px;
        padding: 18px 20px;
        background: white;
        border: 1px solid #e5eaf3;
        border-radius: 13px;
    }

    .cancel-button,
    .save-button {
        padding: 12px 20px;
        border-radius: 9px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: .2s;
    }

    .cancel-button {
        background: white;
        border: 1px solid #dce3ee;
        color: #64748b;
    }

    .cancel-button:hover {
        background: #f8fafc;
    }

    .save-button {
        background: #3659d9;
        color: white;
        border: none;
    }

    .save-button:hover {
        background: #2444b5;
        transform: translateY(-1px);
    }

    @media (max-width: 850px) {
        .edit-layout {
            grid-template-columns: 1fr;
        }

        .cover-preview {
            height: 280px;
        }
    }

    @media (max-width: 550px) {
        .edit-book-page {
            padding: 15px 5px 30px;
        }

        .edit-main-card,
        .edit-side-card {
            padding: 17px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: auto;
        }

        .edit-header h1 {
            font-size: 23px;
        }

        .edit-actions {
            padding: 13px;
        }

        .cancel-button,
        .save-button {
            flex: 1;
            text-align: center;
            padding: 12px 8px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const coverInput = document.getElementById('cover');
        const coverPreview = document.getElementById('coverPreview');
        const coverPlaceholder = document.getElementById('coverPlaceholder');

        coverInput.addEventListener('change', function (event) {
            const file = event.target.files[0];

            if (!file) {
                return;
            }

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!allowedTypes.includes(file.type)) {
                alert('Pilih gambar JPG, JPEG, PNG, atau WEBP.');
                coverInput.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran gambar maksimal 2 MB.');
                coverInput.value = '';
                return;
            }

            const reader = new window['FileReader']();

            reader.onload = function (e) {
                coverPreview.src = e.target.result;
                coverPreview.hidden = false;
                coverPlaceholder.hidden = true;
            };

            reader.readAsDataURL(file);
        });
    });
</script>

@endsection
