@extends('layouts.app')

@section('title', 'Dashboard | LENTERA')

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->role === 'admin';
    $today = now();

    $totalBooks = \App\Models\Book::count();
    $totalStock = \App\Models\Book::sum('stock');
    $totalCategories = \App\Models\Category::count();

    $loanQuery = \App\Models\Loan::query();

    if (!$isAdmin) {
        $loanQuery->where('user_id', $user->id);
    }

    $totalTransactions = (clone $loanQuery)->count();
    $waitingLoans = (clone $loanQuery)->where('status', 'menunggu')->count();
    $approvedLoans = (clone $loanQuery)->where('status', 'disetujui')->count();
    $rejectedLoans = (clone $loanQuery)->where('status', 'ditolak')->count();
    $returnedLoans = (clone $loanQuery)->where('status', 'dikembalikan')->count();

    $recentLoans = (clone $loanQuery)
        ->with(['user', 'book'])
        ->latest()
        ->take(5)
        ->get();

    $popularBooks = \App\Models\Loan::selectRaw('book_id, COUNT(*) as total')
        ->with('book')
        ->when(!$isAdmin, function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->groupBy('book_id')
        ->orderByDesc('total')
        ->take(5)
        ->get();

    $monthlyLoans = (clone $loanQuery)
        ->where('created_at', '>=', now()->copy()->subMonths(5)->startOfMonth())
        ->get()
        ->groupBy(function ($loan) {
            return $loan->created_at->format('Y-m');
        });

    $chartMonths = collect(range(5, 0))->map(function ($offset) use ($monthlyLoans) {
        $date = now()->copy()->subMonths($offset);

        return [
            'label' => $date->translatedFormat('M'),
            'count' => $monthlyLoans->get($date->format('Y-m'), collect())->count(),
        ];
    });

    $maxMonthly = max(1, $chartMonths->max('count'));

    $statusData = [
        ['label' => 'Menunggu', 'count' => $waitingLoans, 'color' => '#f59e0b'],
        ['label' => 'Disetujui', 'count' => $approvedLoans, 'color' => '#3b82f6'],
        ['label' => 'Ditolak', 'count' => $rejectedLoans, 'color' => '#ef4444'],
        ['label' => 'Dikembalikan', 'count' => $returnedLoans, 'color' => '#10b981'],
    ];
@endphp

<style>
    .dashboard-page {
        max-width: 1450px;
        margin: auto;
        padding: 25px 18px 45px;
        color: #1c2945;
    }

    .dashboard-page * {
        box-sizing: border-box;
    }

    /* HEADER */

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .dashboard-greeting h1 {
        margin: 0 0 8px;
        font-size: 25px;
        font-weight: 800;
        color: #1c2945;
    }

    .dashboard-greeting p {
        margin: 0;
        color: #8490a4;
        font-size: 12px;
        line-height: 1.8;
    }

    .date-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 17px;
        background: white;
        border: 1px solid #e8edf5;
        border-radius: 13px;
        box-shadow: 0 4px 15px rgba(28,41,69,.04);
    }

    .date-icon {
        display: grid;
        place-items: center;
        width: 39px;
        height: 39px;
        border-radius: 11px;
        background: #edf2ff;
        font-size: 19px;
    }

    .date-card strong {
        display: block;
        color: #34415c;
        font-size: 11px;
    }

    .date-card small {
        display: block;
        color: #929db0;
        font-size: 10px;
        margin-top: 4px;
    }

    /* WELCOME BANNER */

    .welcome-banner {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        overflow: hidden;
        padding: 32px;
        margin-bottom: 23px;
        border-radius: 18px;
        color: white;
        background:
            radial-gradient(circle at 85% 20%, rgba(255,255,255,.18), transparent 27%),
            linear-gradient(115deg, #2444b5, #3659d9 60%, #168f9b);
        box-shadow: 0 12px 27px rgba(54,89,217,.17);
    }

    .welcome-content {
        position: relative;
        z-index: 1;
        max-width: 650px;
    }

    .welcome-tag {
        display: inline-block;
        padding: 6px 11px;
        margin-bottom: 13px;
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 20px;
        background: rgba(255,255,255,.12);
        color: #e7efff;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .5px;
    }

    .welcome-banner h2 {
        margin: 0 0 10px;
        font-size: clamp(23px, 3vw, 32px);
        font-weight: 800;
    }

    .welcome-banner p {
        max-width: 550px;
        margin: 0;
        color: #e2eaff;
        font-size: 12px;
        line-height: 1.9;
    }

    .welcome-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        padding: 11px 16px;
        border-radius: 9px;
        background: white;
        color: #2444b5 !important;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none !important;
        transition: .2s;
    }

    .welcome-button:hover {
        background: #edf2ff;
        transform: translateY(-2px);
    }

    .welcome-illustration {
        position: relative;
        z-index: 1;
        display: grid;
        place-items: center;
        width: 125px;
        height: 125px;
        flex-shrink: 0;
        border-radius: 30px;
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.17);
        font-size: 65px;
    }

    /* STATISTICS */

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .dashboard-stat {
        position: relative;
        overflow: hidden;
        min-width: 0;
        padding: 19px;
        border: 1px solid #e8edf5;
        border-radius: 15px;
        background: white;
        box-shadow: 0 4px 15px rgba(28,41,69,.04);
        transition: .2s;
    }

    .dashboard-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 9px 22px rgba(28,41,69,.08);
    }

    .stat-heading {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .stat-icon {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        border-radius: 13px;
        font-size: 21px;
    }

    .stat-caption {
        color: #8490a4;
        font-size: 10px;
        font-weight: 600;
    }

    .stat-number {
        margin-top: 5px;
        color: #1c2945;
        font-size: 27px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .stat-footer {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #f0f2f7;
        color: #929db0;
        font-size: 10px;
    }

    .mint .stat-icon {
        background: #e0f8ed;
        color: #059669;
    }

    .blue .stat-icon {
        background: #e3edff;
        color: #3659d9;
    }

    .purple .stat-icon {
        background: #f0eaff;
        color: #805ad5;
    }

    .orange .stat-icon {
        background: #fff0df;
        color: #e77b18;
    }

    /* MAIN GRID */

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr) minmax(230px, .85fr);
        gap: 16px;
        margin-bottom: 20px;
        align-items: stretch;
    }

    .dashboard-card {
        min-width: 0;
        padding: 21px;
        border: 1px solid #e8edf5;
        border-radius: 15px;
        background: white;
        box-shadow: 0 4px 15px rgba(28,41,69,.04);
    }

    .dashboard-card-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        color: #263653;
        font-size: 13px;
        font-weight: 800;
    }

    .title-left {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .title-icon {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        border-radius: 9px;
        background: #edf2ff;
        font-size: 15px;
    }

    .muted-small {
        color: #929db0;
        font-size: 10px;
        font-weight: 500;
    }

    /* CHART */

    .chart-bars {
        display: flex;
        align-items: end;
        justify-content: space-around;
        gap: 10px;
        height: 170px;
        padding: 12px 5px 0;
        border-bottom: 1px solid #e8edf5;
        background: repeating-linear-gradient(
            to bottom,
            transparent 0,
            transparent 39px,
            #edf2f7 40px
        );
    }

    .chart-column {
        display: flex;
        flex: 1;
        flex-direction: column;
        justify-content: end;
        align-items: center;
        height: 100%;
        min-width: 0;
    }

    .chart-bar {
        position: relative;
        width: min(38px, 85%);
        min-height: 4px;
        border-radius: 8px 8px 3px 3px;
        background: linear-gradient(180deg, #5c85ff, #3659d9);
        transition: .25s;
    }

    .chart-bar:hover {
        filter: brightness(1.12);
    }

    .chart-bar.level-1 { height: 10%; }
    .chart-bar.level-2 { height: 20%; }
    .chart-bar.level-3 { height: 30%; }
    .chart-bar.level-4 { height: 40%; }
    .chart-bar.level-5 { height: 50%; }
    .chart-bar.level-6 { height: 60%; }
    .chart-bar.level-7 { height: 70%; }
    .chart-bar.level-8 { height: 80%; }
    .chart-bar.level-9 { height: 90%; }
    .chart-bar.level-10 { height: 100%; }

    .chart-label {
        padding-top: 10px;
        color: #929db0;
        font-size: 10px;
    }

    .chart-description {
        margin: 14px 0 0;
        color: #929db0;
        font-size: 10px;
    }

    /* STATUS */

    .status-entry {
        margin-bottom: 18px;
    }

    .status-line {
        display: grid;
        grid-template-columns: 10px minmax(0, 1fr) auto;
        align-items: center;
        gap: 9px;
        color: #64748b;
        font-size: 11px;
    }

    .status-line strong {
        color: #34415c;
    }

    .status-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
    }

    .status-dot.status-menunggu,
    .status-fill.status-menunggu {
        background: #f59e0b;
    }

    .status-dot.status-disetujui,
    .status-fill.status-disetujui {
        background: #3b82f6;
    }

    .status-dot.status-ditolak,
    .status-fill.status-ditolak {
        background: #ef4444;
    }

    .status-dot.status-dikembalikan,
    .status-fill.status-dikembalikan {
        background: #10b981;
    }

    .status-track {
        height: 7px;
        margin-top: 9px;
        border-radius: 20px;
        background: #edf1f7;
        overflow: hidden;
    }

    .status-fill {
        height: 100%;
        border-radius: 20px;
    }

    .status-fill.width-0 { width: 0%; }
    .status-fill.width-10 { width: 10%; }
    .status-fill.width-20 { width: 20%; }
    .status-fill.width-30 { width: 30%; }
    .status-fill.width-40 { width: 40%; }
    .status-fill.width-50 { width: 50%; }
    .status-fill.width-60 { width: 60%; }
    .status-fill.width-70 { width: 70%; }
    .status-fill.width-80 { width: 80%; }
    .status-fill.width-90 { width: 90%; }
    .status-fill.width-100 { width: 100%; }

    /* QUICK ACTIONS */

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .quick-action {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 13px;
        border: 1px solid #e8edf5;
        border-radius: 11px;
        background: #fafbff;
        color: #34415c !important;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none !important;
        transition: .2s;
    }

    .quick-action:hover {
        background: #edf2ff;
        border-color: #c8d6ff;
        color: #3659d9 !important;
        transform: translateX(3px);
    }

    .quick-action-icon {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        border-radius: 9px;
        background: white;
        font-size: 17px;
    }

    .quick-arrow {
        margin-left: auto;
        color: #9aa6b8;
        font-size: 17px;
    }

    /* BOTTOM SECTION */

    .dashboard-bottom {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(250px, .85fr);
        gap: 16px;
    }

    .dashboard-link {
        color: #3659d9 !important;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none !important;
    }

    .dashboard-link:hover {
        text-decoration: underline !important;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
        font-size: 11px;
    }

    .dashboard-table th {
        padding: 12px 10px;
        background: #f7f9fd;
        color: #7b879b;
        text-align: left;
        font-size: 10px;
        font-weight: 800;
        border-bottom: 1px solid #e8edf5;
    }

    .dashboard-table td {
        padding: 13px 10px;
        border-bottom: 1px solid #edf0f5;
        color: #64748b;
    }

    .dashboard-table tbody tr:hover {
        background: #fafbff;
    }

    .dashboard-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .table-book-name {
        color: #34415c;
        font-weight: 700;
    }

    .table-badge {
        display: inline-block;
        padding: 6px 9px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 800;
    }

    .status-menunggu {
        color: #b7791f;
    }

    .table-badge.status-menunggu {
        background: #fff7e6;
    }

    .table-badge.status-disetujui {
        background: #edf2ff;
        color: #3659d9;
    }

    .table-badge.status-ditolak {
        background: #fff0f1;
        color: #dc4b5a;
    }

    .table-badge.status-dikembalikan {
        background: #e9f9f0;
        color: #15945a;
    }

    /* POPULAR BOOKS */

    .book-ranking {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 13px 0;
        border-bottom: 1px solid #edf2f7;
    }

    .book-ranking:last-child {
        border-bottom: 0;
    }

    .book-rank {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        flex-shrink: 0;
        border-radius: 10px;
        background: #edf2ff;
        color: #3659d9;
        font-size: 11px;
        font-weight: 800;
    }

    .book-ranking-name {
        flex: 1;
        min-width: 0;
        overflow-wrap: anywhere;
        color: #34415c;
        font-size: 11px;
        font-weight: 700;
    }

    .book-ranking-name small {
        display: block;
        margin-top: 5px;
        color: #929db0;
        font-size: 10px;
        font-weight: 400;
    }

    .book-count {
        padding: 6px 9px;
        border-radius: 20px;
        background: #edf2ff;
        color: #3659d9;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .empty-state {
        padding: 30px 12px;
        color: #929db0;
        text-align: center;
        font-size: 11px;
        line-height: 1.9;
    }

    /* RESPONSIVE */

    @media (max-width: 1150px) {
        .dashboard-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .quick-actions-card {
            grid-column: span 2;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .dashboard-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-bottom {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .dashboard-page {
            padding: 18px 10px 35px;
        }

        .dashboard-header {
            align-items: flex-start;
        }

        .dashboard-greeting h1 {
            font-size: 21px;
        }

        .welcome-banner {
            padding: 22px;
        }

        .welcome-illustration {
            width: 75px;
            height: 75px;
            border-radius: 18px;
            font-size: 38px;
        }

        .welcome-banner p {
            font-size: 11px;
        }

        .dashboard-stats {
            gap: 10px;
        }

        .dashboard-stat {
            padding: 13px;
        }

        .stat-heading {
            align-items: flex-start;
            flex-direction: column;
            gap: 9px;
        }

        .stat-number {
            font-size: 22px;
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions-card {
            grid-column: auto;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }

        .dashboard-card {
            padding: 16px;
        }
    }
</style>

<div class="dashboard-page">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div class="dashboard-greeting">
            <h1>
                Halo, {{ explode(' ', $user->name)[0] }}!
                👋
            </h1>
            <p>
                Selamat datang di LENTERA, sistem perpustakaan digital.
                <br>
                Temukan buku menarik dan jelajahi ilmu baru setiap hari.
            </p>
        </div>

        <div class="date-card">
            <div class="date-icon">📅</div>
            <div>
                <strong>{{ $today->translatedFormat('l, d F Y') }}</strong>
                <small>Semangat membaca hari ini!</small>
            </div>
        </div>
    </div>

    {{-- WELCOME BANNER --}}
    <div class="welcome-banner">
        <div class="welcome-content">
            <span class="welcome-tag">
                SISTEM INFORMASI PERPUSTAKAAN
            </span>

            <h2>
                {{ $isAdmin ? 'Selamat Datang, Admin!' : 'Selamat Datang di LENTERA!' }}
                ✨
            </h2>

            <p>
                {{ $isAdmin
                    ? 'Kelola koleksi buku, pantau peminjaman, dan lihat aktivitas perpustakaan dengan mudah.'
                    : 'Temukan buku favoritmu, ajukan peminjaman, dan pantau status pinjamanmu dengan mudah.' }}
            </p>

            <a href="{{ route('books.index') }}" class="welcome-button">
                📚 Jelajahi Katalog Buku <span>→</span>
            </a>
        </div>

        <div class="welcome-illustration">
            📚
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="dashboard-stats">

        <div class="dashboard-stat mint">
            <div class="stat-heading">
                <div class="stat-icon">📚</div>
                <div>
                    <div class="stat-caption">Total Judul Buku</div>
                    <div class="stat-number">
                        {{ number_format($totalBooks, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="stat-footer">
                📦 {{ number_format($totalStock, 0, ',', '.') }} eksemplar dalam stok
            </div>
        </div>

        <div class="dashboard-stat blue">
            <div class="stat-heading">
                <div class="stat-icon">📋</div>
                <div>
                    <div class="stat-caption">
                        {{ $isAdmin ? 'Total Peminjaman' : 'Peminjaman Saya' }}
                    </div>
                    <div class="stat-number">
                        {{ number_format($totalTransactions, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="stat-footer">
                📈 Transaksi yang tercatat
            </div>
        </div>

        <div class="dashboard-stat purple">
            <div class="stat-heading">
                <div class="stat-icon">
                    {{ $isAdmin ? '🗂️' : '⏳' }}
                </div>
                <div>
                    <div class="stat-caption">
                        {{ $isAdmin ? 'Kategori Buku' : 'Menunggu Persetujuan' }}
                    </div>
                    <div class="stat-number">
                        {{ $isAdmin ? $totalCategories : $waitingLoans }}
                    </div>
                </div>
            </div>
            <div class="stat-footer">
                {{ $isAdmin ? 'Kategori koleksi perpustakaan' : 'Pengajuan belum diproses' }}
            </div>
        </div>

        <div class="dashboard-stat orange">
            <div class="stat-heading">
                <div class="stat-icon">✅</div>
                <div>
                    <div class="stat-caption">Buku Dikembalikan</div>
                    <div class="stat-number">
                        {{ number_format($returnedLoans, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="stat-footer">
                📖 Transaksi selesai
            </div>
        </div>

    </div>

    {{-- GRAFIK, STATUS, AKSI CEPAT --}}
    <div class="dashboard-grid">

        {{-- GRAFIK --}}
        <section class="dashboard-card">
            <div class="dashboard-card-title">
                <div class="title-left">
                    <span class="title-icon">📊</span>
                    Statistik Peminjaman
                </div>
                <span class="muted-small">6 bulan terakhir</span>
            </div>

            <div class="chart-bars">
                @foreach ($chartMonths as $month)
                    @php
                        $barLevel = min(10, max(1, (int) ceil(($month['count'] / $maxMonthly) * 10)));
                    @endphp

                    <div class="chart-column">
                        <div
                            class="chart-bar level-{{ $barLevel }}"
                            title="{{ $month['count'] }} peminjaman"
                        ></div>

                        <span class="chart-label">
                            {{ $month['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <p class="chart-description">
                Jumlah transaksi berdasarkan tanggal pengajuan.
            </p>
        </section>

        {{-- STATUS --}}
        <section class="dashboard-card">
            <div class="dashboard-card-title">
                <div class="title-left">
                    <span class="title-icon">📈</span>
                    Status Peminjaman
                </div>
            </div>

            @if ($totalTransactions > 0)

                @foreach ($statusData as $status)
                    @php
                        $statusWidth = ($status['count'] / $totalTransactions) * 100;
                        $statusLevel = min(100, max(0, (int) (round($statusWidth / 10) * 10)));
                        $statusClass = \Illuminate\Support\Str::slug($status['label']);
                    @endphp

                    <div class="status-entry">
                        <div class="status-line">
                            <span class="status-dot status-{{ $statusClass }}"></span>
                            <span>{{ $status['label'] }}</span>
                            <strong>{{ $status['count'] }}</strong>
                        </div>

                        <div class="status-track">
                            <div class="status-fill status-{{ $statusClass }} width-{{ $statusLevel }}"></div>
                        </div>
                    </div>
                @endforeach

            @else
                <div class="empty-state">
                    <div style="font-size:34px">📊</div>
                    Belum ada transaksi peminjaman.
                </div>
            @endif
        </section>

        {{-- AKSI CEPAT --}}
        <section class="dashboard-card quick-actions-card">
            <div class="dashboard-card-title">
                <div class="title-left">
                    <span class="title-icon">⚡</span>
                    Aksi Cepat
                </div>
            </div>

            <div class="quick-actions">

                <a class="quick-action" href="{{ route('books.index') }}">
                    <span class="quick-action-icon">📚</span>
                    <span>Lihat Katalog Buku</span>
                    <span class="quick-arrow">›</span>
                </a>

                <a class="quick-action" href="{{ route('loans.index') }}">
                    <span class="quick-action-icon">📋</span>
                    <span>{{ $isAdmin ? 'Kelola Peminjaman' : 'Lihat Peminjaman' }}</span>
                    <span class="quick-arrow">›</span>
                </a>

                @if ($isAdmin)
                    <a class="quick-action" href="{{ route('categories.index') }}">
                        <span class="quick-action-icon">🗂️</span>
                        <span>Kelola Kategori</span>
                        <span class="quick-arrow">›</span>
                    </a>

                    <a class="quick-action" href="{{ route('reports.index') }}">
                        <span class="quick-action-icon">📈</span>
                        <span>Lihat Laporan</span>
                        <span class="quick-arrow">›</span>
                    </a>
                @endif

            </div>
        </section>

    </div>

    {{-- RIWAYAT DAN BUKU POPULER --}}
    <div class="dashboard-bottom">

        {{-- PEMINJAMAN TERBARU --}}
        <section class="dashboard-card">

            <div class="dashboard-card-title">
                <div class="title-left">
                    <span class="title-icon">🕘</span>
                    {{ $isAdmin ? 'Peminjaman Terbaru' : 'Riwayat Peminjaman Saya' }}
                </div>

                <a href="{{ route('loans.index') }}" class="dashboard-link">
                    Lihat Semua →
                </a>
            </div>

            @if ($recentLoans->isNotEmpty())

                <div class="table-wrapper">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>No.</th>

                                @if ($isAdmin)
                                    <th>Peminjam</th>
                                @endif

                                <th>Judul Buku</th>
                                <th>Status</th>
                                <th>Tanggal Pinjam</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($recentLoans as $loan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    @if ($isAdmin)
                                        <td>
                                            {{ $loan->user?->name ?? 'Pengguna tidak tersedia' }}
                                        </td>
                                    @endif

                                    <td>
                                        <strong class="table-book-name">
                                            {{ $loan->book?->title ?? 'Buku tidak tersedia' }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="table-badge status-{{ $loan->status }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $loan->loan_date
                                            ? \Carbon\Carbon::parse($loan->loan_date)->format('d-m-Y')
                                            : 'Belum diproses' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else

                <div class="empty-state">
                    <div style="font-size:36px">📚</div>
                    Belum ada peminjaman.<br>
                    Mulai jelajahi koleksi buku LENTERA!
                </div>

            @endif

        </section>

        {{-- BUKU POPULER --}}
        <section class="dashboard-card">

            <div class="dashboard-card-title">
                <div class="title-left">
                    <span class="title-icon">🏆</span>
                    Buku Terpopuler
                </div>

                <a href="{{ route('books.index') }}" class="dashboard-link">
                    Katalog →
                </a>
            </div>

            @forelse ($popularBooks as $popular)

                <div class="book-ranking">
                    <div class="book-rank">
                        {{ $loop->iteration }}
                    </div>

                    <div class="book-ranking-name">
                        {{ $popular->book?->title ?? 'Buku tidak tersedia' }}

                        <small>
                            {{ $popular->book?->author ?? 'Penulis tidak tersedia' }}
                        </small>
                    </div>

                    <span class="book-count">
                        {{ $popular->total }}x
                    </span>
                </div>

            @empty

                <div class="empty-state">
                    <div style="font-size:34px">📖</div>
                    Belum ada data buku populer.<br>
                    Daftar akan terisi setelah ada peminjaman.
                </div>

            @endforelse

        </section>

    </div>

</div>
@endsection
