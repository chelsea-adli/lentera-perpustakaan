@extends('layouts.app')

@section('title', 'Dashboard')

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
    .welcome-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 30px;
        margin-bottom: 23px;
        border-radius: 20px;
        color: white;
        background: radial-gradient(circle at 85% 15%, rgba(94,234,212,.3), transparent 30%),
                    linear-gradient(110deg, #063c40, #087e76 65%, #10a99a);
        box-shadow: 0 12px 30px rgba(0,105,92,.13);
        overflow: hidden;
    }

    .welcome-banner h2 {
        margin: 0 0 10px;
        font-size: clamp(23px, 3vw, 32px);
        font-weight: 800;
    }

    .welcome-banner p {
        max-width: 540px;
        margin: 0;
        color: #d4f5ef;
        line-height: 1.8;
    }

    .welcome-icon {
        flex-shrink: 0;
        font-size: 75px;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .dashboard-stat {
        min-width: 0;
        padding: 20px;
        border: 1px solid var(--border);
        border-radius: 17px;
        background: white;
        box-shadow: var(--shadow);
    }

    .stat-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-icon {
        display: grid;
        place-items: center;
        width: 47px;
        height: 47px;
        flex-shrink: 0;
        border-radius: 15px;
        font-size: 22px;
    }

    .stat-caption {
        color: var(--muted);
        font-size: 11px;
        font-weight: 600;
    }

    .stat-number {
        margin-top: 6px;
        font-size: 27px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .stat-footer {
        margin-top: 13px;
        color: var(--muted);
        font-size: 11px;
    }

    .mint .stat-icon { background: #d1fae5; color: #059669; }
    .blue .stat-icon { background: #dbeafe; color: #2563eb; }
    .purple .stat-icon { background: #ede9fe; color: #7c3aed; }
    .orange .stat-icon { background: #ffedd5; color: #ea580c; }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr) minmax(220px, .8fr);
        gap: 17px;
        margin-bottom: 22px;
        align-items: stretch;
    }

    .dashboard-card {
        min-width: 0;
        padding: 21px;
        border: 1px solid var(--border);
        border-radius: 17px;
        background: white;
        box-shadow: var(--shadow);
    }

    .dashboard-card-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 20px;
        font-size: 15px;
        font-weight: 800;
    }

    .muted-small {
        color: var(--muted);
        font-size: 10px;
        font-weight: 500;
    }

    .chart-bars {
        display: flex;
        align-items: end;
        justify-content: space-around;
        gap: 10px;
        height: 165px;
        padding: 12px 5px 0;
        border-bottom: 1px solid var(--border);
        background: repeating-linear-gradient(to bottom, transparent 0, transparent 39px, #edf2f7 40px);
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
        width: min(34px, 85%);
        min-height: 4px;
        border-radius: 7px 7px 2px 2px;
        background: linear-gradient(180deg, #34d3b2, #079887);
    }

    /* Ketinggian grafik menggunakan class agar tidak memicu peringatan CSS Blade */
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
        color: var(--muted);
        font-size: 10px;
    }

    .status-entry {
        margin-bottom: 17px;
    }

    .status-line {
        display: grid;
        grid-template-columns: 11px minmax(0, 1fr) auto;
        align-items: center;
        gap: 9px;
        font-size: 11px;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .status-dot.status-menunggu, .status-fill.status-menunggu { background-color: #f59e0b; }
    .status-dot.status-disetujui, .status-fill.status-disetujui { background-color: #3b82f6; }
    .status-dot.status-ditolak, .status-fill.status-ditolak { background-color: #ef4444; }
    .status-dot.status-dikembalikan, .status-fill.status-dikembalikan { background-color: #10b981; }

    .status-track {
        height: 7px;
        margin-top: 8px;
        border-radius: 20px;
        background: #edf2f7;
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
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #f8fbfd;
        font-size: 11px;
        font-weight: 700;
        transition: .2s;
    }

    .quick-action:hover {
        background: #ecfdf5;
        border-color: #99f6e4;
        transform: translateY(-1px);
    }

    .quick-action-icon {
        font-size: 19px;
    }

    .dashboard-bottom {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(240px, .8fr);
        gap: 17px;
    }

    .dashboard-link {
        color: var(--primary-dark);
        font-size: 11px;
        font-weight: 700;
    }

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
        width: 30px;
        height: 30px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #ccfbf1;
        color: #087f74;
        font-size: 11px;
        font-weight: 800;
    }

    .book-ranking-name {
        flex: 1;
        min-width: 0;
        overflow-wrap: anywhere;
        font-size: 11px;
        font-weight: 700;
    }

    .book-ranking-name small {
        display: block;
        margin-top: 5px;
        color: var(--muted);
        font-weight: 400;
    }

    .book-count {
        padding: 6px 9px;
        border-radius: 20px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 10px;
        white-space: nowrap;
    }

    .empty-state {
        padding: 30px 12px;
        color: var(--muted);
        text-align: center;
        font-size: 12px;
        line-height: 1.8;
    }

    @media (max-width: 1300px) {
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

    @media (max-width: 900px) {
        .dashboard-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-bottom {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .welcome-banner {
            padding: 22px;
        }

        .welcome-icon {
            font-size: 43px;
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

        .dashboard-stats {
            gap: 10px;
        }

        .dashboard-stat {
            padding: 14px;
        }

        .stat-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .stat-number {
            font-size: 22px;
        }
    }
</style>

<div class="page-heading">
    <div>
        <h1>Halo, {{ explode(' ', $user->name)[0] }}! 👋</h1>
        <p>
            Selamat datang di LENTERA, sistem perpustakaan digital.
            <br>Temukan buku menarik dan jelajahi ilmu baru setiap hari.
        </p>
    </div>

    <div class="date-card">
        <strong>📅 {{ $today->translatedFormat('l, d F Y') }}</strong>
        <div style="font-size:11px;margin-top:4px;color:var(--muted)">
            Semangat membaca hari ini! 📖
        </div>
    </div>
</div>

<div class="welcome-banner">
    <div>
        <h2>{{ $isAdmin ? 'Selamat Datang, Admin!' : 'Selamat Datang di LENTERA!' }} ✨</h2>
        <p>
            {{ $isAdmin
                ? 'Kelola koleksi buku, pantau peminjaman, dan lihat aktivitas perpustakaan dengan mudah.'
                : 'Temukan buku favoritmu, ajukan peminjaman, dan pantau status pinjamanmu dengan mudah.' }}
        </p>
        <div style="margin-top:20px">
            <a href="{{ route('books.index') }}" class="btn"
               style="background:white;color:#066b63">
                📚 Jelajahi Katalog Buku →
            </a>
        </div>
    </div>
    <div class="welcome-icon">📚</div>
</div>

<div class="dashboard-stats">
    <div class="dashboard-stat mint">
        <div class="stat-heading">
            <div class="stat-icon">📚</div>
            <div>
                <div class="stat-caption">Total Judul Buku</div>
                <div class="stat-number">{{ number_format($totalBooks, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="stat-footer">📦 {{ number_format($totalStock, 0, ',', '.') }} eksemplar dalam stok</div>
    </div>

    <div class="dashboard-stat blue">
        <div class="stat-heading">
            <div class="stat-icon">📋</div>
            <div>
                <div class="stat-caption">{{ $isAdmin ? 'Total Peminjaman' : 'Peminjaman Saya' }}</div>
                <div class="stat-number">{{ number_format($totalTransactions, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="stat-footer">📈 Transaksi yang tercatat</div>
    </div>

    <div class="dashboard-stat purple">
        <div class="stat-heading">
            <div class="stat-icon">{{ $isAdmin ? '🗂️' : '⏳' }}</div>
            <div>
                <div class="stat-caption">{{ $isAdmin ? 'Kategori Buku' : 'Menunggu Persetujuan' }}</div>
                <div class="stat-number">{{ $isAdmin ? $totalCategories : $waitingLoans }}</div>
            </div>
        </div>
        <div class="stat-footer">{{ $isAdmin ? 'Kategori koleksi perpustakaan' : 'Pengajuan belum diproses' }}</div>
    </div>

    <div class="dashboard-stat orange">
        <div class="stat-heading">
            <div class="stat-icon">✅</div>
            <div>
                <div class="stat-caption">Buku Dikembalikan</div>
                <div class="stat-number">{{ number_format($returnedLoans, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="stat-footer">📖 Transaksi selesai</div>
    </div>
</div>

<div class="dashboard-grid">
    <section class="dashboard-card">
        <div class="dashboard-card-title">
            <span>📊 Statistik Peminjaman</span>
            <span class="muted-small">6 bulan terakhir</span>
        </div>

        <div class="chart-bars">
            @foreach ($chartMonths as $month)
                @php
                    $barLevel = min(10, max(1, (int) ceil(($month['count'] / $maxMonthly) * 10)));
                @endphp
                <div class="chart-column">
                    <div class="chart-bar level-{{ $barLevel }}"
                         title="{{ $month['count'] }} peminjaman"></div>
                    <span class="chart-label">{{ $month['label'] }}</span>
                </div>
            @endforeach
        </div>

        <p class="muted-small" style="margin-top:15px">
            Jumlah transaksi berdasarkan tanggal pengajuan.
        </p>
    </section>

    <section class="dashboard-card">
        <div class="dashboard-card-title">
            <span>🟢 Status Peminjaman</span>
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

    <section class="dashboard-card quick-actions-card">
        <div class="dashboard-card-title">⚡ Aksi Cepat</div>
        <div class="quick-actions">
            <a class="quick-action" href="{{ route('books.index') }}">
                <span class="quick-action-icon">📚</span>
                <span style="flex:1">Lihat Katalog Buku</span> ›
            </a>

            <a class="quick-action" href="{{ route('loans.index') }}">
                <span class="quick-action-icon">📋</span>
                <span style="flex:1">{{ $isAdmin ? 'Kelola Peminjaman' : 'Lihat Peminjaman' }}</span> ›
            </a>

            @if ($isAdmin)
                <a class="quick-action" href="{{ route('categories.index') }}">
                    <span class="quick-action-icon">🗂️</span>
                    <span style="flex:1">Kelola Kategori</span> ›
                </a>

                <a class="quick-action" href="{{ route('reports.index') }}">
                    <span class="quick-action-icon">📈</span>
                    <span style="flex:1">Lihat Laporan</span> ›
                </a>
            @endif
        </div>
    </section>
</div>

<div class="dashboard-bottom">
    <section class="dashboard-card">
        <div class="dashboard-card-title">
            <span>🕘 {{ $isAdmin ? 'Peminjaman Terbaru' : 'Riwayat Peminjaman Saya' }}</span>
            <a href="{{ route('loans.index') }}" class="dashboard-link">Lihat Semua →</a>
        </div>

        @if ($recentLoans->isNotEmpty())
            <div class="table-wrapper">
                <table>
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
                                    <td>{{ $loan->user?->name ?? 'Pengguna tidak tersedia' }}</td>
                                @endif

                                <td>
                                    <strong>{{ $loan->book?->title ?? 'Buku tidak tersedia' }}</strong>
                                </td>

                                <td>
                                    <span class="badge status-{{ $loan->status }}">
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

    <section class="dashboard-card">
        <div class="dashboard-card-title">
            <span>🏆 Buku Terpopuler</span>
            <a href="{{ route('books.index') }}" class="dashboard-link">Katalog →</a>
        </div>

        @forelse ($popularBooks as $popular)
            <div class="book-ranking">
                <div class="book-rank">{{ $loop->iteration }}</div>
                <div class="book-ranking-name">
                    {{ $popular->book?->title ?? 'Buku tidak tersedia' }}
                    <small>{{ $popular->book?->author ?? 'Penulis tidak tersedia' }}</small>
                </div>
                <span class="book-count">{{ $popular->total }}x</span>
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
@endsection
