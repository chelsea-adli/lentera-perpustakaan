<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

abort_unless($user && $user->role === 'admin', 403);

        $totalBooks = Book::sum('stock');
        $totalTitles = Book::count();
        $totalCategories = Category::count();
        $totalUsers = User::where('role', 'siswa')->count();

        $totalLoans = Loan::count();

        $pendingLoans = Loan::where('status', 'menunggu')->count();

        $activeLoans = Loan::where('status', 'disetujui')->count();

        $rejectedLoans = Loan::where('status', 'ditolak')->count();

        $returnedLoans = Loan::where('status', 'dikembalikan')->count();

        $totalFines = Loan::sum('fine');

        $popularBooks = Loan::select(
                'book_id',
                DB::raw('COUNT(*) as total_peminjaman')
            )
            ->with('book')
            ->groupBy('book_id')
            ->orderByDesc('total_peminjaman')
            ->limit(10)
            ->get();

        $recentLoans = Loan::with(['user', 'book'])
            ->latest()
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'totalBooks',
            'totalTitles',
            'totalCategories',
            'totalUsers',
            'totalLoans',
            'pendingLoans',
            'activeLoans',
            'rejectedLoans',
            'returnedLoans',
            'totalFines',
            'popularBooks',
            'recentLoans'
        ));
    }
}
