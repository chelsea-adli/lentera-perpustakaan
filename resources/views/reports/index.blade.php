@extends('layouts.app')

@section('title', 'Laporan Perpustakaan | LENTERA')

@section('content')
<style>
    .report-hero {
        padding: 26px;
        border-radius: 16px;
        background: linear-gradient(135deg, #075e54, #0d9488);
        color: white;
        margin-bottom: 22px;
    }

    .report-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .report-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        background: white;
    }

    .report-card p {
        color: #64748b;
        font-size: 13px;
        margin: 0 0 8px;
    }

    .report-card strong {
        font-size: 26px;
        color: #0f172a;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th,
    .report-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .report-table th {
        background: #f8fafc;
        font-size: 13px;
    }

    .report-table-wrap {
        overflow-x: auto;
    }

    .report-status {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .report-status div {
        padding: 14px;
        border-radius: 10px;
        background: #f8fafc;
    }

    @media(max-width: 800px) {
        .report-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width: 420px) {
        .report-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .report-card {
            padding: 12px;
        }

        .report-card strong {
            font-size: 21px;
        }
    }
</style>

<div class="report-hero">
    <p style="letter-spacing:2px;font-size:12px">LENTERA LIBRARY</p>
    <h1 style="margin-top:0">Laporan Perpustakaan</h1>
    <p>Ringkasan koleksi, pengguna, transaksi, dan aktivitas peminjaman.</p>
</div>

<div class="report-grid">
    <div class="report-card">
        <p>Total Eksemplar Tersedia</p>
        <strong>{{ number_format($totalBooks, 0, ',', '.') }}</strong>
    </div>

    <div class="report-card">
        <p>Judul Buku</p>
        <strong>{{ number_format($totalTitles, 0, ',', '.') }}</strong>
    </div>

    <div class="report-card">
        <p>Kategori</p>
        <strong>{{ number_format($totalCategories, 0, ',', '.') }}</strong>
    </div>

    <div class="report-card">
        <p>Siswa Terdaftar</p>
        <strong>{{ number_format($totalUsers, 0, ',', '.') }}</strong>
    </div>

    <div class="report-card">
        <p>Total Peminjaman</p>
        <strong>{{ number_format($totalLoans, 0, ',', '.') }}</strong>
    </div>

    <div class="report-card">
        <p>Menunggu</p>
        <strong>{{ number_format($pendingLoans, 0, ',', '.') }}</strong>
    </div>

    <div class="report-card">
        <p>Sedang Dipinjam</p>
        <strong>{{ number_format($activeLoans, 0, ',', '.') }}</strong>
    </div>

    <div class="report-card">
        <p>Total Denda Tercatat</p>
        <strong style="font-size:20px">
            Rp{{ number_format($totalFines, 0, ',', '.') }}
        </strong>
    </div>
</div>

<div class="card">
    <h2>Status Peminjaman</h2>

    <div class="report-status">
        <div>
            <p>Menunggu persetujuan</p>
            <strong>{{ $pendingLoans }}</strong>
        </div>

        <div>
            <p>Disetujui / sedang dipinjam</p>
            <strong>{{ $activeLoans }}</strong>
        </div>

        <div>
            <p>Ditolak</p>
            <strong>{{ $rejectedLoans }}</strong>
        </div>

        <div>
            <p>Sudah dikembalikan</p>
            <strong>{{ $returnedLoans }}</strong>
        </div>
    </div>
</div>

<div class="card">
    <h2>10 Buku Terpopuler</h2>

    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Total Peminjaman</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($popularBooks as $bookLoan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $bookLoan->book->title ?? 'Buku dihapus' }}</td>
                        <td>{{ $bookLoan->book->author ?? '-' }}</td>
                        <td>{{ $bookLoan->total_peminjaman }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada data peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>10 Transaksi Terbaru</h2>

    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Buku</th>
                    <th>Status</th>
                    <th>Tanggal Pengajuan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($recentLoans as $loan)
                    <tr>
                        <td>{{ $loan->user->name ?? '-' }}</td>
                        <td>{{ $loan->book->title ?? '-' }}</td>
                        <td>{{ ucfirst($loan->status) }}</td>
                        <td>{{ $loan->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-bottom:20px">
    <a class="btn" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
    <a class="btn" href="{{ route('loans.index') }}">Lihat Peminjaman</a>
</div>
@endsection
