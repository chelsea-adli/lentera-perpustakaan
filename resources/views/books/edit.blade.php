@extends('layouts.app')

@section('title', 'Edit Buku | LENTERA')

@section('content')
<div class="card">
    <h1>Edit Buku</h1>
    <p class="muted">Perbarui informasi buku yang dipilih.</p>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('books.update', $book) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="title">Judul buku</label>
        <input
            id="title"
            name="title"
            value="{{ old('title', $book->title) }}"
            required
        >

        <label for="author">Penulis</label>
        <input
            id="author"
            name="author"
            value="{{ old('author', $book->author) }}"
            required
        >

        <label for="category_id">Kategori</label>
        <select id="category_id" name="category_id" required>
            @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected(old('category_id', $book->category_id) == $category->id)
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <label for="publisher">Penerbit</label>
        <input
            id="publisher"
            name="publisher"
            value="{{ old('publisher', $book->publisher) }}"
        >

        <label for="publication_year">Tahun terbit</label>
        <input
            id="publication_year"
            type="number"
            name="publication_year"
            min="1000"
            max="2100"
            value="{{ old('publication_year', $book->publication_year) }}"
        >

        <label for="isbn">ISBN</label>
        <input
            id="isbn"
            name="isbn"
            value="{{ old('isbn', $book->isbn) }}"
        >

        <label for="stock">Jumlah stok</label>
        <input
            id="stock"
            type="number"
            name="stock"
            min="0"
            value="{{ old('stock', $book->stock) }}"
            required
        >

        <label for="description">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            style="width:100%;padding:12px;border:1px solid #cedadd;border-radius:8px"
        >{{ old('description', $book->description) }}</textarea>

        <button type="submit" style="margin-top:20px">Simpan Perubahan</button>
        <a class="btn" href="{{ route('books.index') }}">Batal</a>
    </form>
</div>
@endsection
