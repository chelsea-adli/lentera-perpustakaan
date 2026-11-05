<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


class LoanController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $loans = Loan::with(['user', 'book'])
            ->when(
                $user->role !== 'admin',
                fn ($query) => $query->where('user_id', $user->id)
            )
            ->latest()
            ->get();

        return view('loans.index', compact('loans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $book = Book::findOrFail($validated['book_id']);

        if ($book->stock < 1) {
            return back()
                ->withInput()
                ->with('error', 'Stok buku sedang habis.');
        }

        $hasActiveLoan = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($hasActiveLoan) {
            return back()
                ->with('error', 'Kamu masih memiliki pengajuan atau peminjaman buku ini.');
        }

        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'menunggu',
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ]);

        return back()->with(
            'success',
            'Pengajuan peminjaman berhasil dikirim. Tunggu persetujuan petugas.'
        );
    }

    public function approve(Loan $loan): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Kamu tidak memiliki izin untuk menyetujui peminjaman.');
        }

        return DB::transaction(function () use ($loan) {
            $loan = Loan::whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($loan->status !== 'menunggu') {
                return back()->with(
                    'error',
                    'Pengajuan ini sudah diproses sebelumnya.'
                );
            }

            $book = Book::whereKey($loan->book_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($book->stock < 1) {
                return back()->with(
                    'error',
                    'Stok buku habis. Pengajuan belum dapat disetujui.'
                );
            }

            $book->decrement('stock');

            $loan->update([
                'status' => 'disetujui',
                'loan_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
            ]);

            return back()->with(
                'success',
                'Peminjaman berhasil disetujui.'
            );
        });
    }

    public function reject(Loan $loan): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Kamu tidak memiliki izin untuk menolak peminjaman.');
        }

        if ($loan->status !== 'menunggu') {
            return back()->with(
                'error',
                'Hanya pengajuan yang menunggu yang dapat ditolak.'
            );
        }

        $loan->update(['status' => 'ditolak']);

        return back()->with(
            'success',
            'Pengajuan peminjaman berhasil ditolak.'
        );
    }

 
public function returnBook(Loan $loan): RedirectResponse
{
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Kamu tidak memiliki izin untuk memproses pengembalian.');
    }

    return DB::transaction(function () use ($loan) {
        $loan = Loan::whereKey($loan->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($loan->status !== 'disetujui') {
            return back()->with(
                'error',
                'Hanya buku yang sedang dipinjam yang dapat dikembalikan.'
            );
        }

        $book = Book::whereKey($loan->book_id)
            ->lockForUpdate()
            ->firstOrFail();

        $returnedAt = now();
        $dueDate = \Illuminate\Support\Carbon::parse($loan->due_date)
            ->startOfDay();

        $returnedDate = $returnedAt->copy()->startOfDay();

        $lateDays = 0;

        if ($returnedDate->greaterThan($dueDate)) {
            $lateDays = (int) $dueDate->diffInDays($returnedDate);
        }

        // Tarif denda: Rp1.000 per hari keterlambatan.
        $finePerDay = 1000;
        $fine = $lateDays * $finePerDay;

        $book->increment('stock');

        $loan->update([
            'status' => 'dikembalikan',
            'returned_at' => $returnedAt,
            'fine' => $fine,
        ]);

        return back()->with(
            'success',
            'Buku berhasil dikembalikan. Terlambat '
            . $lateDays
            . ' hari. Total denda: Rp'
            . number_format($fine, 0, ',', '.')
        );
    });
}

    }
