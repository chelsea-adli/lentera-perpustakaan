@extends('layouts.app')

@section('title', 'Katalog Buku - LENTERA')

@section('content')
<div class="container">

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

    <section class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:20px">
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
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Status</th>
                            @if (auth()->user()->role === 'admin')
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($books as $book)
                            <tr>
                                <td>{{ $books->firstItem() + $loop->index }}</td>

                                <td>
                                    <strong style="color:var(--text)">
                                        {{ $book->title }}
                                    </strong>
                                </td>

                                <td>{{ $book->author }}</td>

                                <td>
                                    {{ $book->category?->name ?? '-' }}
                                </td>

                                <td>
                                    <strong>{{ $book->stock }}</strong>
                                </td>

                                <td>
                                    @if ($book->stock > 0)
                                        <span class="badge" style="background:#dcfce7;color:#15803d">
                                            ● Tersedia
                                        </span>
                                    @else
                                        <span class="badge" style="background:#fee2e2;color:#b91c1c">
                                            ● Habis
                                        </span>
                                    @endif
                                </td>

                                @if (auth()->user()->role === 'admin')
                                    <td>
                                        <div style="display:flex;gap:7px;flex-wrap:wrap">
                                            <a href="{{ route('books.edit', $book) }}"
                                               class="button button-outline"
                                               style="padding:7px 10px">
                                                Edit
                                            </a>

                                            <form action="{{ route('books.destroy', $book) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="button"
                                                        style="padding:7px 10px;background:#dc3545">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:22px">
                {{ $books->withQueryString()->links() }}
            </div>
        @else
            <div style="padding:45px 15px;text-align:center">
                <div style="font-size:48px;margin-bottom:12px">📚</div>
                <h3 style="margin:0 0 8px">Buku belum ditemukan</h3>
                <p style="color:var(--muted);font-size:13px">
                    Coba kata kunci lain atau tekan Reset untuk melihat semua buku.
                </p>
                <a href="{{ route('books.index') }}" class="button">Lihat Semua Buku</a>
            </div>
        @endif
    </section>

</div>
@endsection
