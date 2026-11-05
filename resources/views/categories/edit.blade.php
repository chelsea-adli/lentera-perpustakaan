
@extends('layouts.app')

@section('title', 'Edit Kategori | LENTERA')

@section('content')
<div class="card">
    <h1>Edit Kategori</h1>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="name">Nama kategori</label>
        <input
            id="name"
            name="name"
            value="{{ old('name', $category->name) }}"
            required
        >

        <label for="description">Deskripsi</label>
        <textarea id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>

        <button type="submit">Simpan Perubahan</button>
        <a class="btn" href="{{ route('categories.index') }}">Batal</a>
    </form>
</div>
@endsection
