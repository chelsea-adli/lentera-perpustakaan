
@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Peminjaman Buku</h2>
            <p class="text-muted mb-0">
                Kelola peminjaman dan pengembalian buku perpustakaan LENTERA.
            </p>
        </div>

        <span class="badge rounded-pill bg-primary px-3 py-2">
            {{ auth()->user()->role === 'admin' ? 'Administrator' : 'Anggota Perpustakaan' }}
        </span>
    </div>

    {{-- NOTIFIKASI BERHASIL --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Tutup"></button>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Perhatian!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Tutup"></button>
        </div>
    @endif

    {{-- VALIDASI --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- STATISTIK --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Total Peminjaman</div>
                    <h3 class="fw-bold mb-0">{{ $loans->count() }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Menunggu</div>
                    <h3 class="fw-bold text-warning mb-0">
                        {{ $loans->where('status', 'menunggu')->count() }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Sedang Dipinjam</div>
                    <h3 class="fw-bold text-primary mb-0">
                        {{ $loans->where('status', 'disetujui')->count() }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Total Denda Tercatat</div>
                    <h5 class="fw-bold text-danger mb-0">
                        Rp{{ number_format($loans->sum('fine'), 0, ',', '.') }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM PENGAJUAN UNTUK SISWA --}}
    @if (auth()->user()->role !== 'admin')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Ajukan Peminjaman Buku</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('loans.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="book_id" class="form-label fw-semibold">
                            Pilih Buku
                        </label>

                        <select name="book_id" id="book_id"
                            class="form-select @error('book_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih buku yang ingin dipinjam --</option>

                            @foreach (
                                \App\Models\Book::where('stock', '>', 0)
                                    ->orderBy('title')
                                    ->get() as $book
                            )
                                <option value="{{ $book->id }}"
                                    @selected(old('book_id') == $book->id)>
                                    {{ $book->title }} — Stok tersedia: {{ $book->stock }}
                                </option>
                            @endforeach
                        </select>

                        @error('book_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info small">
                        <strong>Informasi:</strong>
                        Pengajuan akan berstatus menunggu sampai disetujui admin.
                        Batas peminjaman mengikuti tanggal yang ditentukan sistem.
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Ajukan Peminjaman
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- TABEL PEMINJAMAN --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Daftar Peminjaman</h5>
        </div>

        <div class="card-body">
            @if ($loans->isEmpty())
                <div class="text-center py-5">
                    <h5 class="text-muted">Belum Ada Peminjaman</h5>
                    <p class="text-muted mb-0">
                        Data peminjaman akan ditampilkan di sini.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No.</th>

                                @if (auth()->user()->role === 'admin')
                                    <th>Peminjam</th>
                                @endif

                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                                <th>Denda</th>

                                @if (auth()->user()->role === 'admin')
                                    <th class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($loans as $loan)
                                <tr>
                                    <td class="text-center">
                                        {{ $loop->iteration }}
                                    </td>

                                    @if (auth()->user()->role === 'admin')
                                        <td>
                                            {{ $loan->user?->name ?? 'Pengguna tidak ditemukan' }}
                                        </td>
                                    @endif

                                    <td>
                                        <span class="fw-semibold">
                                            {{ $loan->book?->title ?? 'Buku tidak ditemukan' }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $loan->loan_date?->format('d-m-Y') ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $loan->due_date?->format('d-m-Y') ?? '-' }}

                                        @if (
                                            $loan->status === 'disetujui'
                                            && $loan->due_date
                                            && now()->startOfDay()->greaterThan(
                                                $loan->due_date->copy()->startOfDay()
                                            )
                                        )
                                            <div class="small text-danger fw-semibold mt-1">
                                                Terlambat
                                                {{ (int) $loan->due_date
                                                    ->diffInDays(now()->startOfDay()) }}
                                                hari
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        @switch($loan->status)
                                            @case('menunggu')
                                                <span class="badge bg-warning text-dark">
                                                    Menunggu
                                                </span>
                                                @break

                                            @case('disetujui')
                                                <span class="badge bg-primary">
                                                    Sedang Dipinjam
                                                </span>
                                                @break

                                            @case('ditolak')
                                                <span class="badge bg-danger">
                                                    Ditolak
                                                </span>
                                                @break

                                            @case('dikembalikan')
                                                <span class="badge bg-success">
                                                    Dikembalikan
                                                </span>
                                                @break

                                            @default
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($loan->status) }}
                                                </span>
                                        @endswitch
                                    </td>

                                    <td>
                                        @if (($loan->fine ?? 0) > 0)
                                            <span class="text-danger fw-bold">
                                                Rp{{ number_format($loan->fine, 0, ',', '.') }}
                                            </span>
                                        @elseif ($loan->status === 'dikembalikan')
                                            <span class="text-success">
                                                Rp0
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                Belum final
                                            </span>
                                        @endif
                                    </td>

                                    @if (auth()->user()->role === 'admin')
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-center gap-2">

                                                @if ($loan->status === 'menunggu')
                                                    <form
                                                        action="{{ route('loans.approve', $loan) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Setujui peminjaman buku ini?')">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                            class="btn btn-success btn-sm">
                                                            Setujui
                                                        </button>
                                                    </form>

                                                    <form
                                                        action="{{ route('loans.reject', $loan) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Tolak pengajuan ini?')">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                            class="btn btn-danger btn-sm">
                                                            Tolak
                                                        </button>
                                                    </form>

                                                @elseif ($loan->status === 'disetujui')
                                                    <form
                                                        action="{{ route('loans.return', $loan) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Proses pengembalian buku dan hitung dendanya?')">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                            class="btn btn-primary btn-sm">
                                                            Kembalikan
                                                        </button>
                                                    </form>

                                                @else
                                                    <span class="text-muted small">
                                                        Tidak ada aksi
                                                    </span>
                                                @endif

                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot class="table-light">
                            <tr>
                                <th colspan="{{ auth()->user()->role === 'admin' ? 6 : 5 }}"
                                    class="text-end">
                                    Total Denda Tercatat
                                </th>

                                <th class="text-danger">
                                    Rp{{ number_format($loans->sum('fine'), 0, ',', '.') }}
                                </th>

                                @if (auth()->user()->role === 'admin')
                                    <th></th>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- KETERANGAN DENDA --}}
    <div class="alert alert-light border mt-4 mb-0">
        <h6 class="fw-bold">Informasi Denda Keterlambatan</h6>
        <p class="mb-1">
            Tarif denda: <strong>Rp1.000 per hari keterlambatan.</strong>
        </p>
        <p class="small text-muted mb-0">
            Denda final dihitung dan disimpan saat admin memproses pengembalian buku.
            Buku yang dikembalikan tepat waktu tidak dikenai denda.
        </p>
    </div>

</div>
@endsection
