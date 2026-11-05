<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $totalBooks = Book::count();
        $totalCategories = Category::count();

        if ($user->role === 'admin') {
            $totalLoans = Loan::count();

            $pendingLoans = Loan::where(
                'status',
                'menunggu'
            )->count();

            return view('dashboard', compact(
                'user',
                'totalBooks',
                'totalCategories',
                'totalLoans',
                'pendingLoans'
            ));
        }

        $myLoans = Loan::where(
            'user_id',
            $user->id
        )->count();

        return view('dashboard', compact(
            'user',
            'totalBooks',
            'totalCategories',
            'myLoans'
        ));
    }
}