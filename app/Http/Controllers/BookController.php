<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Menampilkan katalog buku dan pencarian.
     */
    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $books = $query
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        return view('books.index', compact('books'));
    }

    /**
     * Menampilkan form tambah buku.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('books.create', compact('categories'));
    }

    /**
     * Menyimpan buku baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')
                ->store('covers', 'public');
        }

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(Book $book)
    {
        $book->load('category');

        return view('books.show', compact('book'));
    }

    /**
     * Menampilkan form edit buku.
     */
    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Memperbarui data buku.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }

            $validated['cover'] = $request->file('cover')
                ->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Menghapus buku.
     */
    public function destroy(Book $book)
    {
        // Jangan menghapus buku yang masih memiliki riwayat peminjaman.
        if ($book->loans()->exists()) {
            return redirect()
                ->route('books.index')
                ->with('error', 'Buku tidak bisa dihapus karena memiliki riwayat peminjaman.');
        }

        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}