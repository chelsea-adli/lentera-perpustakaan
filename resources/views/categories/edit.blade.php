@extends('layouts.app')

@section('title', 'Edit Kategori | LENTERA')

@section('content')

<div class="category-page">

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <div class="breadcrumb">
                MANAJEMEN KATEGORI <span>/</span> EDIT KATEGORI
            </div>

            <h1>Edit Kategori</h1>

            <p>Perbarui informasi kategori buku perpustakaan LENTERA.</p>
        </div>

        <a href="{{ route('categories.index') }}" class="back-button">
            ← Kembali ke Kategori
        </a>
    </div>

    {{-- ERROR VALIDASI --}}
    @if ($errors->any())
        <div class="error-message">
            <strong>Terjadi kesalahan!</strong>

            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- FORM EDIT --}}
    <div class="category-card">

        <div class="card-heading">
            <div class="heading-icon">📚</div>

            <div>
                <h2>Informasi Kategori</h2>
                <p>Ubah nama dan deskripsi kategori buku.</p>
            </div>
        </div>

        <form
            action="{{ route('categories.update', $category) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">
                    Nama Kategori <span>*</span>
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $category->name) }}"
                    placeholder="Contoh: Pendidikan"
                    maxlength="255"
                    required
                >

                <small>Masukkan nama kategori buku yang ingin digunakan.</small>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Kategori</label>

                <textarea
                    id="description"
                    name="description"
                    rows="6"
                    placeholder="Tuliskan penjelasan singkat mengenai kategori ini..."
                >{{ old('description', $category->description) }}</textarea>

                <small>Deskripsi bersifat opsional.</small>
            </div>

            {{-- INFORMASI --}}
            <div class="info-box">
                <span>💡</span>
                <p>
                    Perubahan kategori akan digunakan pada data buku
                    yang menggunakan kategori ini.
                </p>
            </div>

            {{-- TOMBOL --}}
            <div class="form-actions">
                <a
                    href="{{ route('categories.index') }}"
                    class="cancel-button"
                >
                    Batal
                </a>

                <button type="submit" class="save-button">
                    ✓ Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>

<style>
    .category-page {
        max-width: 1000px;
        margin: 0 auto;
        padding: 25px 10px 45px;
        color: #1c2945;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .breadcrumb {
        color: #3659d9;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 9px;
    }

    .breadcrumb span {
        color: #94a3b8;
        margin: 0 6px;
    }

    .page-header h1 {
        font-size: 27px;
        margin: 0 0 7px;
        color: #1c2945;
    }

    .page-header p {
        color: #718096;
        font-size: 13px;
        margin: 0;
        line-height: 1.6;
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
        transition: .2s;
    }

    .back-button:hover {
        background: #f0f4ff;
    }

    .error-message {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
        padding: 15px 18px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 20px;
        line-height: 1.8;
    }

    .category-card {
        background: white;
        border: 1px solid #e5eaf3;
        border-radius: 15px;
        padding: 28px;
        box-shadow: 0 4px 18px rgba(28, 41, 69, .05);
    }

    .card-heading {
        display: flex;
        align-items: center;
        gap: 13px;
        padding-bottom: 22px;
        margin-bottom: 25px;
        border-bottom: 1px solid #edf0f5;
    }

    .heading-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #edf2ff;
        border-radius: 12px;
        font-size: 22px;
    }

    .card-heading h2 {
        font-size: 17px;
        margin: 0 0 5px;
        color: #1c2945;
    }

    .card-heading p {
        font-size: 12px;
        color: #8994a7;
        margin: 0;
    }

    .form-group {
        margin-bottom: 23px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #34415c;
        margin-bottom: 9px;
    }

    .form-group label span {
        color: #e05252;
    }

    .form-group input,
    .form-group textarea {
        display: block;
        width: 100%;
        box-sizing: border-box;
        padding: 13px 15px;
        border: 1px solid #dce3ee;
        border-radius: 9px;
        background: #fff;
        color: #26344f;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: .2s;
    }

    .form-group input {
        min-height: 47px;
    }

    .form-group textarea {
        min-height: 150px;
        resize: vertical;
        line-height: 1.7;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #3659d9;
        box-shadow: 0 0 0 3px rgba(54, 89, 217, .10);
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #a0aabd;
    }

    .form-group small {
        display: block;
        margin-top: 8px;
        font-size: 11px;
        color: #8994a7;
    }

    .info-box {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        background: #f0f5ff;
        border: 1px solid #dce7ff;
        border-radius: 10px;
        margin-bottom: 25px;
    }

    .info-box span {
        font-size: 17px;
    }

    .info-box p {
        margin: 0;
        color: #52658b;
        font-size: 12px;
        line-height: 1.7;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 11px;
        padding-top: 20px;
        border-top: 1px solid #edf0f5;
    }

    .cancel-button,
    .save-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 21px;
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

    @media (max-width: 600px) {
        .category-page {
            padding: 15px 5px 30px;
        }

        .category-card {
            padding: 19px;
        }

        .page-header h1 {
            font-size: 23px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .cancel-button,
        .save-button {
            width: 100%;
            box-sizing: border-box;
        }
    }
</style>

@endsection
