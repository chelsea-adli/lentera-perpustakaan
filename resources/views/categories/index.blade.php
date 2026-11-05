@extends('layouts.app')

@section('title', 'Kategori Buku | LENTERA')

@section('content')

<style>
    .categories-page {
        padding: 25px;
        color: #1e293b;
    }

    .categories-page * {
        box-sizing: border-box;
    }

    /* HEADER HALAMAN */
    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        padding: 5px 0 25px;
        margin-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .category-header h1 {
        margin: 0 0 7px;
        font-size: 27px;
        color: #1e293b;
    }

    .category-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    /* TOMBOL */
    .category-btn {
        display: inline-block;
        padding: 11px 17px;
        border-radius: 9px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        border: none;
        cursor: pointer;
        transition: .2s;
    }

    .category-btn:hover {
        transform: translateY(-2px);
        opacity: .9;
    }

    .category-btn-blue {
        background: #3659d9;
        color: white;
    }

    .category-btn-edit {
        background: #e8efff;
        color: #3659d9;
    }

    .category-btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    /* NOTIFIKASI */
    .category-alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .category-success {
        background: #dcfce7;
        color: #166534;
    }

    .category-error {
        background: #fee2e2;
        color: #991b1b;
    }

    /* JUDUL DAFTAR */
    .category-section-title {
        margin: 0 0 18px;
        font-size: 20px;
        color: #1e293b;
    }

    /* GRID KATEGORI */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }

    /* KARTU */
    .category-item {
        background: white;
        border: 1px solid #e5eaf5;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .04);
        transition: .25s;
    }

    .category-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(54, 89, 217, .12);
        border-color: #b8c8ff;
    }

    .category-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        border-radius: 13px;
        font-size: 23px;
        margin-bottom: 15px;
    }

    .category-item h3 {
        font-size: 19px;
        margin: 0 0 9px;
        color: #1e293b;
    }

    .category-description {
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        min-height: 42px;
        margin-bottom: 15px;
    }

    .category-book-count {
        display: inline-block;
        padding: 7px 11px;
        background: #eff6ff;
        color: #3659d9;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 18px;
    }

    /* AKSI EDIT DAN HAPUS */
    .category-actions {
        display: flex;
        gap: 9px;
        padding-top: 15px;
        border-top: 1px solid #f1f5f9;
    }

    .category-actions form {
        margin: 0;
    }

    /* KATEGORI KOSONG */
    .category-empty {
        background: white;
        padding: 40px 20px;
        text-align: center;
        border-radius: 15px;
        border: 1px dashed #cbd5e1;
        color: #64748b;
        grid-column: 1 / -1;
    }

    .category-empty h3 {
        color: #1e293b;
    }

    /* PAGINATION */
    .category-pagination {
        margin-top: 25px;
    }

    /* RESPONSIVE */
    @media (max-width: 600px) {
        .categories-page {
            padding: 15px;
        }

        .category-header {
            align-items: flex-start;
        }

        .category-header h1 {
            font-size: 23px;
        }

        .category-header-actions {
            width: 100%;
        }

        .category-header-actions .category-btn {
            text-align: center;
        }

        .category-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="categories-page">

    {{-- HEADER --}}
    <div class="category-header">
        <div>
            <h1>📚 Kelola Kategori Buku</h1>
            <p>
                Atur dan kelola kategori koleksi buku perpustakaan LENTERA.
            </p>
        </div>

        <div class="category-header-actions">
            <a href="{{ route('categories.create') }}"
               class="category-btn category-btn-blue">
                + Tambah Kategori
            </a>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="category-alert category-success">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="category-alert category-error">
            ⚠ {{ session('error') }}
        </div>
    @endif

    {{-- DAFTAR KATEGORI --}}
    <h2 class="category-section-title">
        Daftar Kategori
    </h2>

    <div class="category-grid">

        @forelse ($categories as $category)

            <div class="category-item">

                <div class="category-icon">
                    📚
                </div>

                <h3>{{ $category->name }}</h3>

                <div class="category-description">
                    {{ $category->description ?: 'Belum ada deskripsi untuk kategori ini.' }}
                </div>

                <div class="category-book-count">
                    📖 {{ $category->books_count }} Buku
                </div>

                <div class="category-actions">

                    {{-- EDIT --}}
                    <a href="{{ route('categories.edit', $category) }}"
                       class="category-btn category-btn-edit">
                        ✏ Edit
                    </a>

                    {{-- HAPUS --}}
                    <form
                        action="{{ route('categories.destroy', $category) }}"
                        method="POST"
                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus kategori ini?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="category-btn category-btn-delete">
                            🗑 Hapus
                        </button>
                    </form>

                </div>

            </div>

        @empty

            <div class="category-empty">
                <div style="font-size:45px; margin-bottom:12px;">
                    📚
                </div>

                <h3>Belum Ada Kategori</h3>

                <p>Silakan tambahkan kategori buku terlebih dahulu.</p>

                <a href="{{ route('categories.create') }}"
                   class="category-btn category-btn-blue">
                    + Tambah Kategori
                </a>
            </div>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="category-pagination">
        {{ $categories->links() }}
    </div>

</div>

@endsection
