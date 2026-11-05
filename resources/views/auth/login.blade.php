
@extends('layouts.app')

@section('title', 'Masuk | LENTERA')

@push('styles')
<style>
    .auth-page {
        min-height: 82vh;
        padding: 35px 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(circle at 10% 10%, #dce6ff 0, transparent 30%),
            radial-gradient(circle at 90% 90%, #dff6f1 0, transparent 30%),
            #f3f6fc;
    }

    .auth-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        width: min(920px, 100%);
        overflow: hidden;
        border-radius: 24px;
        background: white;
        box-shadow: 0 20px 65px #253d7618;
    }

    .auth-banner {
        padding: 48px 38px;
        color: white;
        background: linear-gradient(145deg, #4267e8, #233d9c);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .auth-logo {
        display: grid;
        place-items: center;
        width: 65px;
        height: 65px;
        margin-bottom: 25px;
        border: 1px solid #ffffff55;
        border-radius: 19px;
        background: #ffffff20;
        font-size: 35px;
        font-weight: 800;
    }

    .auth-banner h1 {
        font-size: 34px;
        line-height: 1.3;
        margin: 0 0 15px;
    }

    .auth-banner p {
        color: #e2e9ff;
        font-size: 14px;
        line-height: 1.9;
    }

    .auth-features {
        margin-top: 18px;
        line-height: 2.4;
        font-size: 13px;
    }

    .auth-form {
        padding: 48px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .auth-form h2 {
        font-size: 28px;
        margin: 0 0 8px;
    }

    .auth-subtitle {
        color: #8390a7;
        font-size: 13px;
        line-height: 1.8;
        margin: 0 0 28px;
    }

    .auth-submit {
        width: 100%;
        margin-top: 6px;
    }

    .auth-bottom {
        margin-top: 23px;
        text-align: center;
        color: #7b879d;
        font-size: 13px;
    }

    .auth-bottom a {
        font-weight: 700;
    }

    @media (max-width: 700px) {
        .auth-card {
            grid-template-columns: 1fr;
        }

        .auth-banner {
            padding: 28px;
        }

        .auth-banner h1 {
            font-size: 27px;
        }

        .auth-logo {
            width: 50px;
            height: 50px;
            font-size: 27px;
            margin-bottom: 15px;
        }

        .auth-features {
            line-height: 2;
        }

        .auth-form {
            padding: 32px 25px;
        }
    }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <section class="auth-banner">
            <div class="auth-logo">L</div>

            <h1>Selamat Datang di LENTERA</h1>

            <p>
                Satu langkah menuju lebih banyak pengetahuan.
                Jelajahi koleksi buku dan kelola peminjaman
                dengan lebih mudah melalui perpustakaan digital.
            </p>

            <div class="auth-features">
                <div>✓ Jelajahi katalog buku</div>
                <div>✓ Ajukan peminjaman online</div>
                <div>✓ Pantau status dan riwayat</div>
            </div>
        </section>

        <section class="auth-form">
            <h2>Masuk Akun</h2>

            <p class="auth-subtitle">
                Masukkan email dan password untuk melanjutkan.
            </p>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Belum berhasil masuk.</strong>
                    <ul style="margin:8px 0 0;padding-left:20px">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.authenticate') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="button auth-submit">
                    Masuk ke LENTERA →
                </button>
            </form>

            <div class="auth-bottom">
                Belum punya akun?
                <a href="{{ route('register') }}">Daftar sekarang</a>
            </div>
        </section>

    </div>
</div>
@endsection
