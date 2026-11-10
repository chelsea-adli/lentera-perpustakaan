@extends('layouts.app')

@section('title', 'Peminjaman Buku | LENTERA')

@section('content')

<div class="loans-page">

    {{-- HEADER --}}
    <div class="loans-header">
        <div>
            <div class="loans-breadcrumb">LENTERA / TRANSAKSI</div>
            <h1>Peminjaman Buku</h1>
            <p>Kelola peminjaman dan pengembalian buku perpustakaan dengan mudah.</p>
        </div>

        <div class="role-label">
            <span class="role-dot"></span>
            {{ auth()->user()->role === 'admin' ? 'Administrator' : 'Anggota Perpustakaan' }}
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="notice notice-success">
            <span>✓</span>
            <div><strong>Berhasil!</strong> {{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="notice notice-error">
            <span>!</span>
            <div><strong>Perhatian!</strong> {{ session('error') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="notice notice-error">
            <span>!</span>
            <div>
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- STATISTIK --}}
    <div class="stats-grid">

        <div class="stat-card stat-blue">
            <div class="stat-top">
                <div class="stat-icon">📚</div>
                <span class="stat-caption">Seluruh transaksi</span>
            </div>
            <div class="stat-number">{{ $loans->count() }}</div>
            <div class="stat-title">Total Peminjaman</div>
        </div>

        <div class="stat-card stat-orange">
            <div class="stat-top">
                <div class="stat-icon">⏳</div>
                <span class="stat-caption">Perlu diperiksa</span>
            </div>
            <div class="stat-number">
                {{ $loans->where('status', 'menunggu')->count() }}
            </div>
            <div class="stat-title">Menunggu Persetujuan</div>
        </div>

        <div class="stat-card stat-purple">
            <div class="stat-top">
                <div class="stat-icon">📖</div>
                <span class="stat-caption">Belum dikembalikan</span>
            </div>
            <div class="stat-number">
                {{ $loans->where('status', 'disetujui')->count() }}
            </div>
            <div class="stat-title">Sedang Dipinjam</div>
        </div>

        <div class="stat-card stat-red">
            <div class="stat-top">
                <div class="stat-icon">💰</div>
                <span class="stat-caption">Akumulasi denda</span>
            </div>
            <div class="stat-number stat-currency">
                Rp{{ number_format($loans->sum('fine'), 0, ',', '.') }}
            </div>
            <div class="stat-title">Total Denda Tercatat</div>
        </div>

    </div>

    {{-- FORM PENGAJUAN SISWA --}}
    @if (auth()->user()->role !== 'admin')

        <div class="content-card borrow-card">

            <div class="card-heading">
                <div class="heading-icon">📝</div>
                <div>
                    <h2>Ajukan Peminjaman Buku</h2>
                    <p>Pilih buku yang ingin kamu pinjam dari perpustakaan.</p>
                </div>
            </div>

            <form action="{{ route('loans.store') }}" method="POST">
                @csrf

                <div class="borrow-form">

                    <div class="form-group">
                        <label for="book_id">Pilih Buku</label>

                        <select
                            name="book_id"
                            id="book_id"
                            class="@error('book_id') input-error @enderror"
                            required
                        >
                            <option value="">-- Pilih buku yang ingin dipinjam --</option>

                            @foreach (
                                \App\Models\Book::where('stock', '>', 0)
                                    ->orderBy('title')
                                    ->get() as $book
                            )
                                <option
                                    value="{{ $book->id }}"
                                    @selected(old('book_id') == $book->id)
                                >
                                    {{ $book->title }} — Stok tersedia: {{ $book->stock }}
                                </option>
                            @endforeach
                        </select>

                        @error('book_id')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="borrow-info">
                        <span>💡</span>
                        <div>
                            <strong>Informasi Peminjaman</strong>
                            <p>
                                Pengajuan akan berstatus menunggu sampai disetujui admin.
                                Batas peminjaman mengikuti tanggal yang ditentukan sistem.
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="primary-button">
                        Ajukan Peminjaman <span>→</span>
                    </button>

                </div>
            </form>

        </div>

    @endif

    {{-- DAFTAR PEMINJAMAN --}}
    <div class="content-card history-card">

        <div class="card-heading history-heading">
            <div class="heading-icon">📋</div>

            <div>
                <h2>Daftar Peminjaman</h2>
                <p>Informasi seluruh transaksi peminjaman buku.</p>
            </div>

            <div class="total-label">
                {{ $loans->count() }} Transaksi
            </div>
        </div>

        @if ($loans->isEmpty())

            <div class="empty-state">
                <div class="empty-icon">📚</div>
                <h3>Belum Ada Peminjaman</h3>
                <p>Data peminjaman akan ditampilkan di sini.</p>
            </div>

        @else

            <div class="table-container">
                <table class="loans-table">

                    <thead>
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
                                <td class="text-center">{{ $loop->iteration }}</td>

                                @if (auth()->user()->role === 'admin')
                                    <td>
                                        <div class="borrower-name">
                                            <span class="borrower-avatar">
                                                {{ strtoupper(substr($loan->user?->name ?? 'P', 0, 1)) }}
                                            </span>
                                            {{ $loan->user?->name ?? 'Pengguna tidak ditemukan' }}
                                        </div>
                                    </td>
                                @endif

                                <td>
                                    <span class="book-name">
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
                                        <div class="late-label">
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
                                            <span class="status-badge status-waiting">
                                                <span></span> Menunggu
                                            </span>
                                            @break

                                        @case('disetujui')
                                            <span class="status-badge status-approved">
                                                <span></span> Sedang Dipinjam
                                            </span>
                                            @break

                                        @case('ditolak')
                                            <span class="status-badge status-rejected">
                                                <span></span> Ditolak
                                            </span>
                                            @break

                                        @case('dikembalikan')
                                            <span class="status-badge status-returned">
                                                <span></span> Dikembalikan
                                            </span>
                                            @break

                                        @default
                                            <span class="status-badge status-other">
                                                {{ ucfirst($loan->status) }}
                                            </span>

                                    @endswitch
                                </td>

                                <td>
                                    @if (($loan->fine ?? 0) > 0)
                                        <span class="fine-value">
                                            Rp{{ number_format($loan->fine, 0, ',', '.') }}
                                        </span>
                                    @elseif ($loan->status === 'dikembalikan')
                                        <span class="fine-free">Rp0</span>
                                    @else
                                        <span class="fine-pending">Belum final</span>
                                    @endif
                                </td>

                                @if (auth()->user()->role === 'admin')
                                    <td>
                                        <div class="action-buttons">

                                            @if ($loan->status === 'menunggu')

                                                <form
                                                    action="{{ route('loans.approve', $loan) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Setujui peminjaman buku ini?')"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit" class="action-button approve-button">
                                                        Setujui
                                                    </button>
                                                </form>

                                                <form
                                                    action="{{ route('loans.reject', $loan) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Tolak pengajuan ini?')"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit" class="action-button reject-button">
                                                        Tolak
                                                    </button>
                                                </form>

                                            @elseif ($loan->status === 'disetujui')

                                                <form
                                                    action="{{ route('loans.return', $loan) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Proses pengembalian buku dan hitung dendanya?')"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit" class="action-button return-button">
                                                        Kembalikan
                                                    </button>
                                                </form>

                                            @else

                                                <span class="no-action">Tidak ada aksi</span>

                                            @endif

                                        </div>
                                    </td>
                                @endif

                            </tr>

                        @endforeach

                    </tbody>

                    <tfoot>
                        <tr>
                            <td
                                colspan="{{ auth()->user()->role === 'admin' ? 6 : 5 }}"
                                class="total-footer"
                            >
                                Total Denda Tercatat
                            </td>

                            <td class="footer-fine">
                                Rp{{ number_format($loans->sum('fine'), 0, ',', '.') }}
                            </td>

                            @if (auth()->user()->role === 'admin')
                                <td></td>
                            @endif
                        </tr>
                    </tfoot>

                </table>
            </div>

        @endif

    </div>

    {{-- INFORMASI DENDA --}}
    <div class="fine-information">
        <div class="fine-info-icon">ℹ️</div>

        <div>
            <h3>Informasi Denda Keterlambatan</h3>
            <p>
                Tarif denda sebesar <strong>Rp1.000 per hari keterlambatan.</strong>
            </p>
            <p class="fine-info-detail">
                Denda final dihitung dan disimpan saat admin memproses pengembalian buku.
                Buku yang dikembalikan tepat waktu tidak dikenai denda.
            </p>
        </div>
    </div>

</div>

<style>
    .loans-page {
        max-width: 1250px;
        margin: 0 auto;
        padding: 30px 15px 45px;
        color: #1c2945;
    }

    .loans-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 28px;
    }

    .loans-breadcrumb {
        color: #3659d9;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 9px;
    }

    .loans-header h1 {
        font-size: 27px;
        font-weight: 800;
        margin: 0 0 7px;
        color: #1c2945;
    }

    .loans-header p {
        color: #7b879b;
        font-size: 13px;
        margin: 0;
    }

    .role-label {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        border-radius: 30px;
        background: #edf2ff;
        color: #3659d9;
        font-size: 11px;
        font-weight: 800;
    }

    .role-dot {
        width: 8px;
        height: 8px;
        background: #3659d9;
        border-radius: 50%;
    }

    .notice {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 17px;
        border-radius: 10px;
        margin-bottom: 18px;
        font-size: 13px;
        line-height: 1.7;
    }

    .notice > span {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 23px;
        height: 23px;
        flex-shrink: 0;
        border-radius: 50%;
        font-weight: 800;
    }

    .notice-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #bbf7d0;
    }

    .notice-success > span {
        background: #d1fae5;
    }

    .notice-error {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }

    .notice-error > span {
        background: #ffe4e6;
    }

    .notice ul {
        margin: 7px 0 0;
        padding-left: 18px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 25px;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        min-width: 0;
        padding: 20px;
        background: white;
        border: 1px solid #e8edf5;
        border-radius: 14px;
        box-shadow: 0 4px 15px rgba(28, 41, 69, .04);
    }

    .stat-card::after {
        content: "";
        position: absolute;
        right: -22px;
        bottom: -35px;
        width: 95px;
        height: 95px;
        border-radius: 50%;
        opacity: .07;
        background: currentColor;
    }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 17px;
    }

    .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        border-radius: 12px;
        font-size: 20px;
    }

    .stat-caption {
        color: #929db0;
        font-size: 10px;
        text-align: right;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 5px;
        overflow-wrap: anywhere;
    }

    .stat-currency {
        font-size: 19px;
    }

    .stat-title {
        color: #8490a4;
        font-size: 11px;
        font-weight: 600;
    }

    .stat-blue {
        color: #3659d9;
    }

    .stat-blue .stat-icon {
        background: #edf2ff;
    }

    .stat-orange {
        color: #d97706;
    }

    .stat-orange .stat-icon {
        background: #fff7e6;
    }

    .stat-purple {
        color: #805ad5;
    }

    .stat-purple .stat-icon {
        background: #f3edff;
    }

    .stat-red {
        color: #dc4b5a;
    }

    .stat-red .stat-icon {
        background: #fff0f1;
    }

    .content-card {
        background: white;
        border: 1px solid #e8edf5;
        border-radius: 15px;
        box-shadow: 0 4px 18px rgba(28, 41, 69, .04);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .card-heading {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 21px 24px;
        border-bottom: 1px solid #edf0f5;
    }

    .heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        background: #edf2ff;
        border-radius: 12px;
        font-size: 20px;
    }

    .card-heading h2 {
        font-size: 16px;
        font-weight: 800;
        color: #1c2945;
        margin: 0 0 5px;
    }

    .card-heading p {
        color: #929db0;
        font-size: 11px;
        margin: 0;
    }

    .borrow-form {
        padding: 23px 24px 25px;
    }

    .form-group {
        margin-bottom: 17px;
    }

    .form-group label {
        display: block;
        color: #34415c;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 9px;
    }

    .form-group select {
        display: block;
        width: 100%;
        box-sizing: border-box;
        min-height: 47px;
        padding: 11px 14px;
        border: 1px solid #dce3ee;
        border-radius: 9px;
        background: white;
        color: #34415c;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: .2s;
    }

    .form-group select:focus {
        border-color: #3659d9;
        box-shadow: 0 0 0 3px rgba(54, 89, 217, .10);
    }

    .form-group select.input-error {
        border-color: #e05252;
    }

    .field-error {
        display: block;
        color: #dc2626;
        font-size: 11px;
        margin-top: 7px;
    }

    .borrow-info {
        display: flex;
        gap: 11px;
        align-items: flex-start;
        padding: 14px 16px;
        background: #f0f5ff;
        border: 1px solid #dce7ff;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .borrow-info > span {
        font-size: 18px;
    }

    .borrow-info strong {
        color: #3659d9;
        font-size: 12px;
    }

    .borrow-info p {
        color: #64748b;
        font-size: 11px;
        line-height: 1.7;
        margin: 4px 0 0;
    }

    .primary-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 13px 21px;
        background: #3659d9;
        color: white;
        border: none;
        border-radius: 9px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: .2s;
    }

    .primary-button:hover {
        background: #2444b5;
        transform: translateY(-1px);
    }

    .primary-button span {
        font-size: 17px;
    }

    .history-heading {
        flex-wrap: wrap;
    }

    .total-label {
        margin-left: auto;
        padding: 8px 12px;
        background: #edf2ff;
        color: #3659d9;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .loans-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
        font-size: 11px;
    }

    .loans-table th {
        background: #f8faff;
        color: #65738b;
        font-weight: 800;
        text-align: left;
        padding: 15px 13px;
        border-bottom: 1px solid #e9edf4;
    }

    .loans-table td {
        padding: 15px 13px;
        border-bottom: 1px solid #edf0f5;
        color: #526078;
        vertical-align: middle;
    }

    .loans-table tbody tr:hover {
        background: #fafbff;
    }

    .loans-table tbody tr:last-child td {
        border-bottom: none;
    }

    .text-center {
        text-align: center !important;
    }

    .borrower-name {
        display: flex;
        align-items: center;
        gap: 9px;
        color: #34415c;
        font-weight: 700;
    }

    .borrower-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 29px;
        height: 29px;
        border-radius: 50%;
        background: #edf2ff;
        color: #3659d9;
        font-weight: 800;
    }

    .book-name {
        color: #34415c;
        font-weight: 700;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
    }

    .status-badge > span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-waiting {
        background: #fff7e6;
        color: #b7791f;
    }

    .status-approved {
        background: #edf2ff;
        color: #3659d9;
    }

    .status-rejected {
        background: #fff0f1;
        color: #dc4b5a;
    }

    .status-returned {
        background: #e9f9f0;
        color: #15945a;
    }

    .status-other {
        background: #f1f5f9;
        color: #64748b;
    }

    .late-label {
        color: #dc4b5a;
        font-size: 10px;
        font-weight: 800;
        margin-top: 5px;
    }

    .fine-value {
        color: #dc4b5a;
        font-weight: 800;
    }

    .fine-free {
        color: #15945a;
        font-weight: 700;
    }

    .fine-pending {
        color: #929db0;
        font-size: 10px;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
    }

    .action-buttons form {
        margin: 0;
    }

    .action-button {
        border: none;
        border-radius: 7px;
        padding: 8px 10px;
        font-family: inherit;
        font-size: 10px;
        font-weight: 800;
        cursor: pointer;
        transition: .2s;
    }

    .approve-button {
        background: #e9f9f0;
        color: #15945a;
    }

    .approve-button:hover {
        background: #c9f2dc;
    }

    .reject-button {
        background: #fff0f1;
        color: #dc4b5a;
    }

    .reject-button:hover {
        background: #ffe0e3;
    }

    .return-button {
        background: #edf2ff;
        color: #3659d9;
    }

    .return-button:hover {
        background: #dce6ff;
    }

    .no-action {
        color: #a0aabd;
        font-size: 10px;
    }

    .loans-table tfoot td {
        padding: 16px 13px;
        background: #f8faff;
        border-top: 1px solid #e9edf4;
        font-weight: 800;
    }

    .total-footer {
        text-align: right;
        color: #526078;
    }

    .footer-fine {
        color: #dc4b5a !important;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        font-size: 45px;
        margin-bottom: 13px;
    }

    .empty-state h3 {
        font-size: 16px;
        color: #34415c;
        margin: 0 0 8px;
    }

    .empty-state p {
        font-size: 12px;
        color: #929db0;
        margin: 0;
    }

    .fine-information {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        padding: 18px 20px;
        background: white;
        border: 1px solid #e5eaf3;
        border-radius: 12px;
    }

    .fine-info-icon {
        font-size: 21px;
    }

    .fine-information h3 {
        color: #34415c;
        font-size: 13px;
        font-weight: 800;
        margin: 0 0 8px;
    }

    .fine-information p {
        color: #64748b;
        font-size: 11px;
        line-height: 1.7;
        margin: 0 0 4px;
    }

    .fine-information .fine-info-detail {
        color: #929db0;
    }

    @media (max-width: 950px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .loans-page {
            padding: 20px 10px 35px;
        }

        .loans-header h1 {
            font-size: 23px;
        }

        .stats-grid {
            gap: 10px;
        }

        .stat-card {
            padding: 14px;
        }

        .stat-number {
            font-size: 23px;
        }

        .stat-currency {
            font-size: 16px;
        }

        .stat-caption {
            font-size: 9px;
        }

        .card-heading {
            padding: 17px;
        }

        .borrow-form {
            padding: 18px;
        }

        .history-heading {
            gap: 10px;
        }

        .history-heading .total-label {
            width: 100%;
            margin-left: 57px;
            margin-top: -5px;
            text-align: center;
            box-sizing: border-box;
        }

        .primary-button {
            width: 100%;
        }

        .fine-information {
            padding: 15px;
        }
    }
</style>

@endsection
