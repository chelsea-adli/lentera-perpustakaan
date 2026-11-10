@extends('layouts.app')

@section('title', 'Katalog Buku - LENTERA')

@section('content')

<div class="container">

    {{-- HEADER --}}
    <div style="margin-bottom:24px">
        <div style="color:var(--teal);font-size:12px;font-weight:800;letter-spacing:1px">
            PERPUSTAKAAN DIGITAL
        </div>

        <h1 style="font-size:29px;margin:8px 0;color:var(--text)">
            Katalog Buku LENTERA 📚
        </h1>

        <p style="color:var(--muted);margin:0;line-height:1.7">
            Temukan buku favoritmu dan mulai petualangan membaca.
        </p>
    </div>

    @php
        $totalBooks = $books->total();
        $currentPage = $books->currentPage();
        $perPage = $books->perPage();
        $firstItem = $books->firstItem() ?? 0;
        $lastItem = $books->lastItem() ?? 0;
    @endphp

    {{-- STATISTIK --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px;margin-bottom:24px">

        <div class="card">
            <div style="font-size:26px;margin-bottom:12px">📚</div>
            <div style="font-size:12px;color:var(--muted)">Total Buku Ditemukan</div>
            <div style="font-size:27px;font-weight:800;margin-top:5px">
                {{ number_format($totalBooks, 0, ',', '.') }}
            </div>
        </div>

        <div class="card">
            <div style="font-size:26px;margin-bottom:12px">📄</div>
            <div style="font-size:12px;color:var(--muted)">Halaman Saat Ini</div>
            <div style="font-size:27px;font-weight:800;margin-top:5px">
                {{ $currentPage }}
                <span style="font-size:14px;color:var(--muted)">/ {{ max(1, $books->lastPage()) }}</span>
            </div>
        </div>

        <div class="card">
            <div style="font-size:26px;margin-bottom:12px">📋</div>
            <div style="font-size:12px;color:var(--muted)">Jumlah per Halaman</div>
            <div style="font-size:27px;font-weight:800;margin-top:5px">
                {{ $perPage }}
            </div>
        </div>
    </div>

    {{-- PENCARIAN --}}
    <section class="card" style="margin-bottom:24px">
        <h2 style="font-size:17px;margin:0 0 6px">🔎 Cari Buku</h2>

        <p style="font-size:12px;color:var(--muted);margin:0 0 18px">
            Cari berdasarkan judul, penulis, atau kategori buku.
        </p>

        <form action="{{ route('books.index') }}" method="GET">
            <div style="display:flex;gap:10px;flex-wrap:wrap">

                <input
                    type="search"
                    name="search"
                    class="form-control"
                    style="flex:1;min-width:200px"
                    placeholder="Masukkan judul, penulis, atau kategori..."
                    value="{{ request('search') }}"
                >

                <button type="submit" class="button">🔍 Cari</button>

                <a href="{{ route('books.index') }}" class="button button-outline">
                    Reset
                </a>

            </div>
        </form>
    </section>

    {{-- DAFTAR BUKU --}}
    <section class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:22px">

            <div>
                <h2 style="font-size:18px;margin:0 0 5px">📖 Daftar Buku</h2>

                <p style="font-size:12px;color:var(--muted);margin:0">
                    Menampilkan {{ $firstItem }}–{{ $lastItem }} dari {{ $totalBooks }} buku.
                </p>
            </div>

            @if (auth()->user()->role === 'admin')
                <a href="{{ route('books.create') }}" class="button">
                    + Tambah Buku
                </a>
            @endif

        </div>

        @if ($books->count())

            <div class="book-grid">

                @foreach ($books as $book)

                    <div class="book-card">

                        {{-- GAMBAR BUKU --}}
                        <div class="book-image">

                            @if (!empty($book->cover))
                                <img
                                    src="{{ asset('storage/' . $book->cover) }}"
                                    alt="Sampul {{ $book->title }}"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >

                                <div class="book-placeholder" style="display:none">
                                    <span>📚</span>
                                    <small>LENTERA BOOK</small>
                                </div>
                            @else
                                <div class="book-placeholder">
                                    <span>📚</span>
                                    <small>LENTERA BOOK</small>
                                </div>
                            @endif

                            @if ($book->stock > 0)
                                <span class="book-status available">
                                    ● Tersedia
                                </span>
                            @else
                                <span class="book-status unavailable">
                                    ● Habis
                                </span>
                            @endif

                        </div>

                        {{-- INFORMASI BUKU --}}
                        <div class="book-content">

                            <div class="book-category">
                                {{ $book->category?->name ?? 'Umum' }}
                            </div>

                            <h3 class="book-title">
                                {{ $book->title }}
                            </h3>

                            <p class="book-author">
                                Penulis: {{ $book->author }}
                            </p>

                            <p class="book-stock">
                                Stok tersedia: <strong>{{ $book->stock }}</strong>
                            </p>

                            {{-- TOMBOL DESKRIPSI DAN PINJAM --}}
                            <div class="book-buttons">

                                <details class="description-box">
                                    <summary>Deskripsi</summary>

                                    <div class="description-content">
                                        {{ $book->description ?? 'Belum ada deskripsi untuk buku ini.' }}
                                    </div>
                                </details>

                                @if (auth()->user()->role !== 'admin')

                                    @if ($book->stock > 0)
                                        <form
                                            action="{{ route('loans.store') }}"
                                            method="POST"
                                            style="flex:1"
                                        >
                                            @csrf

                                            <input
                                                type="hidden"
                                                name="book_id"
                                                value="{{ $book->id }}"
                                            >

                                            <button type="submit" class="borrow-button">
                                                Pinjam
                                            </button>
                                        </form>
                                    @else
                                        <button class="borrow-button disabled" disabled>
                                            Habis
                                        </button>
                                    @endif

                                @else

                                    <a
                                        href="{{ route('books.edit', $book) }}"
                                        class="borrow-button"
                                        style="text-decoration:none;text-align:center"
                                    >
                                        Edit
                                    </a>

                                @endif

                            </div>

                            {{-- AKSI ADMIN --}}
                            @if (auth()->user()->role === 'admin')
                                <form
                                    action="{{ route('books.destroy', $book) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus buku ini?')"
                                    style="margin-top:9px"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="delete-button"
                                    >
                                        Hapus Buku
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>

                @endforeach

            </div>

            {{-- PAGINATION --}}
            <div style="margin-top:25px">
                {{ $books->withQueryString()->links() }}
            </div>

        @else

            <div style="padding:45px 15px;text-align:center">
                <div style="font-size:48px;margin-bottom:12px">📚</div>

                <h3 style="margin:0 0 8px">Buku belum ditemukan</h3>

                <p style="color:var(--muted);font-size:13px">
                    Coba kata kunci lain atau tekan Reset untuk melihat semua buku.
                </p>

                <a href="{{ route('books.index') }}" class="button">
                    Lihat Semua Buku
                </a>
            </div>

        @endif

    </section>

</div>

{{-- CSS KHUSUS CARD BUKU --}}
<style>
    .book-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .book-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        background: white;
        transition: .2s ease;
    }

    .book-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0,0,0,.08);
    }

    .book-image {
        height: 210px;
        background: #eef2ff;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .book-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
    }

    .book-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        height: 100%;
        color: #3659d9;
    }

    .book-placeholder span {
        font-size: 65px;
    }

    .book-placeholder small {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 2px;
    }

    .book-status {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .available {
        background: #dcfce7;
        color: #15803d;
    }

    .unavailable {
        background: #fee2e2;
        color: #b91c1c;
    }

    .book-content {
        padding: 16px;
    }

    .book-category {
        color: #3659d9;
        font-size: 11px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .book-title {
        font-size: 16px;
        line-height: 1.4;
        color: #1c2945;
        margin: 0 0 7px;
    }

    .book-author,
    .book-stock {
        font-size: 12px;
        color: #64748b;
        margin: 5px 0;
    }

    .book-buttons {
        display: flex;
        gap: 8px;
        margin-top: 16px;
        align-items: flex-start;
    }

    .description-box {
        flex: 1;
        min-width: 0;
    }

    .description-box summary {
        list-style: none;
        cursor: pointer;
        text-align: center;
        padding: 10px 6px;
        border: 1px solid #3659d9;
        color: #3659d9;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .description-box summary::-webkit-details-marker {
        display: none;
    }

    .description-content {
        margin-top: 9px;
        padding: 10px;
        background: #f8fafc;
        border-radius: 8px;
        color: #475569;
        font-size: 12px;
        line-height: 1.6;
    }

    .borrow-button {
        display: block;
        width: 100%;
        padding: 10px 6px;
        border: none;
        border-radius: 8px;
        background: #3659d9;
        color: white;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .borrow-button:hover {
        background: #2444b5;
    }

    .borrow-button.disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    .delete-button {
        width: 100%;
        padding: 8px;
        border: none;
        border-radius: 8px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    @media (max-width: 900px) {
        .book-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 550px) {
        .book-grid {
            grid-template-columns: 1fr;
        }

        .book-image {
            height: 230px;
        }
    }
</style>

@endsection
