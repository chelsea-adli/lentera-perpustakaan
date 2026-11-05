
@extends('layouts.app')

@section('title', 'Daftar | LENTERA')

@push('styles')
<style>
    .register-page {
        min-height: 82vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 35px 18px;
        background: linear-gradient(135deg, #edf2ff, #f3f6fc, #e7f8f3);
    }

    .register-card {
        width: min(480px, 100%);
        padding: 38px;
        border: 1px solid #e6ebf5;
        border-radius: 22px;
        background: white;
        box-shadow: 0 20px 60px #253d7614;
    }

    .register-logo {
        display: grid;
        place-items: center;
        width: 55px;
        height: 55px;
        margin-bottom: 22px;
        border-radius: 16px;
        color: white;
        background: #3659d9;
        font-size: 28px;
        font-weight: 800;
    }

    .register-card h1 {
        margin: 0 0 8px;
        font-size: 28px;
    }

    .register-description {
        margin-bottom: 26px;
        color: #8290a8;
        font-size: 13px;
        line-height: 1.8;
    }

    .register-submit {
        width: 100%;
        margin-top: 5px;
    }

    .register-bottom {
        margin-top: 23px;
        text-align: center;
        color: #7b879d;
        font-size: 13px;
    }

    @media (max-width: 480px) {
        .register-card {
            padding: 27px 22px;
        }
    }
</style>
@endpush

@section('content')
<div class="register-page">
    <div class="register-card">
        <div class="register-logo">L</div>

        <h1>Buat Akun LENTERA</h1>

        <p class="register-description">
            Daftar untuk mengakses katalog dan layanan
            peminjaman buku perpustakaan.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0;padding-left:20px">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}"
                    placeholder="Masukkan nama lengkap"
                    autocomplete="name"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Minimal 8 karakter"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    Konfirmasi Password
                </label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="Ulangi password"
                    autocomplete="new-password"
                    required
                >
            </div>

            <button type="submit" class="button register-submit">
                Buat Akun →
            </button>
        </form>

        <div class="register-bottom">
            Sudah punya akun?
            <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
