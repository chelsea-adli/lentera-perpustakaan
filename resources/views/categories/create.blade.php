@extends('layouts.app')

@section('title', 'Tambah Kategori | LENTERA')

@section('content')

<style>
    .create-category-page {
        padding: 30px 20px;
        color: #1e293b;
    }

    .create-category-page * {
        box-sizing: border-box;
    }

    .create-category-container {
        max-width: 750px;
        margin: 15px auto;
    }

    /* HEADER */
    .create-category-heading {
        margin-bottom: 25px;
    }

    .create-category-heading h1 {
        font-size: 27px;
        color: #1e293b;
        margin: 0 0 8px;
    }

    .create-category-heading p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }

    /* CARD FORM */
    .create-category-card {
        background: #ffffff;
        border: 1px solid #e5eaf5;
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 8px 25px rgba(15, 23, 42, .06);
    }

    .create-category-card-title {
        display: flex;
        align-items: center;
        gap: 14px;
        padding-bottom: 22px;
        margin-bottom: 25px;
        border-bottom: 1px solid #e2e8f0;
    }

    .create-category-icon {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        border-radius: 14px;
        font-size: 25px;
    }

    .create-category-card-title h2 {
        font-size: 19px;
        color: #1e293b;
        margin: 0 0 5px;
    }

    .create-category-card-title p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    /* FORM */
    .create-category-form .form-group {
        margin-bottom: 22px;
    }

    .create-category-form label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 9px;
    }

    .create-category-form .required {
        color: #dc2626;
    }

    .create-category-form input,
    .create-category-form textarea {
        display: block;
        width: 100%;
        padding: 13px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        color: #1e293b;
        font-family: inherit;
        font-size: 14px;
        outline: none;
        transition: .2s;
    }

    .create-category-form input {
        height: 48px;
    }

    .create-category-form textarea {
        min-height: 130px;
        resize: vertical;
        line-height: 1.6;
    }

    .create-category-form input:focus,
    .create-category-form textarea:focus {
        border-color: #3659d9;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(54, 89, 217, .12);
    }

    .create-category-form input::placeholder,
    .create-category-form textarea::placeholder {
        color: #94a3b8;
    }

    .form-hint {
        color: #94a3b8;
        font-size: 12px;
        margin-top: 7px;
    }

    /* ERROR */
    .create-category-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 15px 18px;
        margin-bottom: 22px;
        font-size: 13px;
    }

    .create-category-error strong {
        display: block;
        margin-bottom: 8px;
    }

    .create-category-error ul {
        padding-left: 20px;
        margin: 0;
    }

    .create-category-error li {
        margin-bottom: 4px;
    }

    /* TOMBOL */
    .create-category-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .create-category-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 7px;
        padding: 12px 20px;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: .2s;
    }

    .create-category-btn:hover {
        transform: translateY(-2px);
    }

    .create-category-btn-save {
        background: #3659d9;
        color: white;
    }

    .create-category-btn-save:hover {
        background: #2446bd;
    }

    .create-category-btn-cancel {
        background: #f1f5f9;
        color: #475569;
    }

    .create-category-btn-cancel:hover {
        background: #e2e8f0;
    }

    /* RESPONSIVE */
    @media (max-width: 600px) {
        .create-category-page {
            padding: 20px 14px;
        }

        .create-category-heading h1 {
            font-size: 23px;
        }

        .create-category-card {
            padding: 20px;
        }

        .create-category-actions {
            flex-direction: column-reverse;
        }

        .create-category-btn {
            width: 100%;
        }
    }
</style>

<div class="create-category-page">

    <div class="create-category-container">

        {{-- HEADER --}}
        <div class="create-category-heading">
            <h1>Tambah Kategori</h1>
            <p>Tambahkan kategori baru untuk mengelompokkan koleksi buku LENTERA.</p>
        </div>

        {{-- CARD FORM --}}
        <div class="create-category-card">

            <div class="create-category-card-title">
                <div class="create-category-icon">
                    📚
                </div>

                <div>
                    <h2>Informasi Kategori</h2>
                    <p>Lengkapi informasi kategori buku berikut.</p>
                </div>
            </div>

            {{-- VALIDASI ERROR --}}
            @if ($errors->any())
                <div class="create-category-error">
                    <strong>⚠ Periksa kembali data yang kamu masukkan:</strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM TAMBAH KATEGORI --}}
            <form
                action="{{ route('categories.store') }}"
                method="POST"
                class="create-category-form"
            >
                @csrf

                <div class="form-group">
                    <label for="name">
                        Nama Kategori <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Fiksi, Pendidikan, Teknologi"
                        required
                    >

                    <div class="form-hint">
                        Masukkan nama kategori buku yang ingin ditambahkan.
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">
                        Deskripsi Kategori
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        placeholder="Tuliskan penjelasan singkat mengenai kategori buku ini..."
                    >{{ old('description') }}</textarea>

                    <div class="form-hint">
                        Deskripsi bersifat opsional.
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="create-category-actions">

                    <a
                        href="{{ route('categories.index') }}"
                        class="create-category-btn create-category-btn-cancel"
                    >
                        ← Batal
                    </a>

                    <button
                        type="submit"
                        class="create-category-btn create-category-btn-save"
                    >
                        ✓ Simpan Kategori
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
