<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#3659d9">

    <title>@yield('title', 'LENTERA')</title>

    <style>
        :root {
            --primary: #3659d9;
            --primary-dark: #2444b5;
            --teal: #087e76;
            --text: #1c2945;
            --muted: #71809b;
            --background: #f3f6fc;
            --border: #e1e7f2;
            --shadow: 0 4px 16px rgba(28, 41, 69, .06);
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--background);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 14px;
            overflow-x: hidden;
        }

        a {
            color: var(--primary);
            text-decoration: none;
        }

        a:hover {
            text-decoration: none;
        }

        button, input, select, textarea {
            font: inherit;
            max-width: 100%;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            position: relative;
            z-index: 100;
            min-height: 68px;
            padding: 14px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            background: #fff;
            border-bottom: 1px solid var(--border);
        }

        .brand {
            color: var(--primary);
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .nav-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 20px;
        }

        .nav-links > a {
            color: #52617c;
            font-size: 13px;
            font-weight: 600;
            transition: .2s;
        }

        .nav-links > a:hover {
            color: var(--primary);
        }

        .nav-user {
            color: var(--text);
            font-size: 13px;
        }

        .nav-links form {
            margin: 0;
        }

        /* Tombol hamburger */

        .nav-toggle {
            display: none;
            width: 42px;
            height: 42px;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--primary);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 5px;
        }

        .nav-toggle span {
            display: block;
            width: 20px;
            height: 2px;
            border-radius: 5px;
            background: currentColor;
            transition: .2s;
        }

        .nav-toggle:hover {
            background: #f1f5ff;
        }

        /* =========================
           MAIN CONTENT
        ========================= */

        main {
            min-height: calc(100vh - 130px);
            width: 100%;
        }

        .container {
            width: min(1400px, 92%);
            margin: 0 auto;
            padding: 30px 0;
        }

        /* =========================
           BUTTON
        ========================= */

        .button, button.button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 11px 17px;
            border: 1px solid transparent;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s ease;
        }

        .button:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        .button-teal {
            background: var(--teal);
        }

        .button-teal:hover {
            background: #06645e;
        }

        .button-outline {
            color: var(--text);
            background: #fff;
            border-color: var(--border);
        }

        .button-outline:hover {
            background: #f8fafc;
            color: var(--primary);
        }

        /* =========================
           FORM
        ========================= */

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
        }

        .form-control {
            display: block;
            width: 100%;
            min-height: 42px;
            padding: 11px 13px;
            border: 1px solid #d8e0ef;
            border-radius: 10px;
            background: #fff;
            color: var(--text);
            font-size: 13px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(54, 89, 217, .10);
        }

        /* =========================
           ALERT
        ========================= */

        .alert {
            padding: 13px 15px;
            margin-bottom: 18px;
            border-radius: 10px;
            font-size: 13px;
        }

        .alert-success {
            color: #17633d;
            background: #e8f8ef;
            border: 1px solid #bce9ce;
        }

        .alert-danger {
            color: #a12b36;
            background: #fff0f1;
            border: 1px solid #f5c5ca;
        }

        /* =========================
           PAGE HEADING
        ========================= */

        .page-heading {
            width: min(1400px, 92%);
            margin: 0 auto;
            padding: 25px 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .page-heading h1 {
            margin: 0 0 10px;
            font-size: 27px;
            font-weight: 800;
        }

        .page-heading p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .date-card {
            padding: 14px 17px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fff;
            box-shadow: var(--shadow);
            white-space: nowrap;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            padding: 22px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            box-shadow: var(--shadow);
        }

        /* =========================
           TABLE
        ========================= */

        .table-wrap,
        .table-wrapper {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f7f9fd;
            color: #64718a;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        td {
            font-size: 13px;
        }

        tbody tr:hover {
            background: #fafcff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 20px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 11px;
            font-weight: 700;
        }

        /* =========================
           FOOTER
        ========================= */

        .footer {
            padding: 22px 12px;
            text-align: center;
            color: #8b97ac;
            font-size: 11px;
        }

        /* =========================
           TABLET
        ========================= */

        @media (max-width: 900px) {
            .navbar {
                padding-left: 4%;
                padding-right: 4%;
                gap: 12px;
            }

            .nav-links {
                gap: 12px;
            }

            .container {
                width: 94%;
            }

            .page-heading {
                width: 94%;
            }
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 760px) {

            /* Navbar HP */

            .navbar {
                min-height: auto;
                padding: 12px 5%;
                display: block;
            }

            .nav-top {
                width: 100%;
            }

            .brand {
                font-size: 23px;
            }

            .nav-toggle {
                display: flex;
            }

            .nav-links {
                display: none;
                width: 100%;
                padding: 15px 0 5px;
                margin-top: 12px;
                border-top: 1px solid var(--border);
                flex-direction: column;
                align-items: stretch;
                justify-content: flex-start;
                gap: 0;
            }

            .nav-links.is-open {
                display: flex;
            }

            .nav-links > a {
                display: block;
                padding: 13px 10px;
                border-radius: 8px;
                font-size: 14px;
            }

            .nav-links > a:hover {
                background: #f1f5ff;
            }

            .nav-user {
                display: block;
                padding: 12px 10px;
                border-top: 1px solid var(--border);
                margin-top: 5px;
            }

            .nav-links form {
                width: 100%;
                padding: 8px 0 3px;
            }

            .nav-links form button {
                width: 100%;
                min-height: 43px;
            }

            /* Konten */

            .container {
                width: 92%;
                padding: 22px 0;
            }

            .page-heading {
                width: 92%;
                align-items: flex-start;
                flex-direction: column;
                padding: 20px 0;
            }

            .page-heading h1 {
                font-size: 23px;
            }

            .date-card {
                white-space: normal;
                width: 100%;
            }

            .card {
                padding: 16px;
            }

            /* Form */

            input,
            select,
            textarea {
                max-width: 100%;
            }

            /* Tabel */

            .table-wrap table,
            .table-wrapper table {
                min-width: 600px;
            }

            /* Tombol */

            .button,
            button.button {
                min-height: 40px;
            }

            /* Footer */

            .footer {
                padding: 20px 12px;
            }
        }

        /* =========================
           HP KECIL
        ========================= */

        @media (max-width: 380px) {
            .brand {
                font-size: 21px;
            }

            .container {
                width: 94%;
            }

            .page-heading h1 {
                font-size: 21px;
            }

            .card {
                padding: 13px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    @auth
        <header class="navbar">

            <div class="nav-top">
                <a href="{{ route('dashboard') }}" class="brand">
                    LENTERA
                </a>

                <button
                    type="button"
                    class="nav-toggle"
                    id="navToggle"
                    aria-label="Buka menu navigasi"
                    aria-expanded="false"
                    aria-controls="navLinks"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <nav class="nav-links" id="navLinks">

                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <a href="{{ route('books.index') }}">
                    Katalog Buku
                </a>

                <a href="{{ route('loans.index') }}">
                    Peminjaman
                </a>

                <span class="nav-user">
                    {{ auth()->user()->name }}
                </span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="button">
                        Keluar
                    </button>
                </form>

            </nav>

        </header>
    @endauth

    <main>

        @if (session('success'))
            <div class="container" style="padding-bottom:0">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container" style="padding-bottom:0">
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')

    </main>

    <footer class="footer">
        &copy; {{ date('Y') }} LENTERA · Sistem Informasi Perpustakaan
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const toggle = document.getElementById('navToggle');
            const nav = document.getElementById('navLinks');

            if (toggle && nav) {

                toggle.addEventListener('click', function () {

                    const isOpen = nav.classList.toggle('is-open');

                    toggle.setAttribute(
                        'aria-expanded',
                        isOpen ? 'true' : 'false'
                    );

                    toggle.setAttribute(
                        'aria-label',
                        isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi'
                    );

                });

                // Tutup menu setelah memilih halaman
                nav.querySelectorAll('a').forEach(function (link) {

                    link.addEventListener('click', function () {
                        nav.classList.remove('is-open');
                        toggle.setAttribute('aria-expanded', 'false');
                    });

                });

            }

        });
    </script>

    @stack('scripts')

</body>
</html>
