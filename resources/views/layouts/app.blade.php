<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BorrowBox - Campus Item Sharing')</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f8faf9;
            color: #24352f;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        header {
            background: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5ebe7;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
            color: #267653;
            text-decoration: none;
        }

        nav {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
        }

        nav a {
            text-decoration: none;
            color: #34483d;
            font-size: 14px;
            font-weight: 500;
        }

        nav a:hover {
            color: #27845a;
        }

        nav .add-item {
            background: #27845a;
            color: white;
            padding: 9px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        nav .add-item:hover {
            background: #1e6846;
        }

        .nav-bell {
            position: relative;
            text-decoration: none;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            color: #34483d;
        }

        .nav-badge {
            background: #e53935;
            color: white;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
            padding: 1px 6px;
            margin-left: 3px;
        }

        .btn {
            display: inline-block;
            background: #27845a;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            transition: background 0.15s ease;
        }

        .btn:hover {
            background: #1e6846;
        }

        .btn-secondary {
            background: #e5ebe7;
            color: #24352f;
        }

        .btn-secondary:hover {
            background: #d8e2dc;
        }

        .btn-danger {
            background: #c62828;
            color: white;
        }

        .btn-danger:hover {
            background: #a71d1d;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .btn-outline {
            background: transparent;
            color: #27845a;
            border: 1px solid #27845a;
        }

        .btn-outline:hover {
            background: #f0f8f3;
        }

        /* Common layout card */
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 28px;
            border-radius: 14px;
            border: 1px solid #e5ebe7;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        }

        .container-narrow {
            max-width: 520px;
        }

        .container-wide {
            max-width: 1140px;
        }

        .page-header {
            margin-bottom: 24px;
            padding-bottom: 14px;
            border-bottom: 1px solid #eef2f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-title {
            font-size: 24px;
            font-weight: bold;
            color: #253858;
        }

        .page-subtitle {
            color: #667085;
            font-size: 14px;
            margin-top: 4px;
        }

        /* Form elements matching create.blade.php */
        label {
            display: block;
            margin-top: 16px;
            font-weight: bold;
            font-size: 14px;
            color: #253858;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="number"],
        input[type="search"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            box-sizing: border-box;
            padding: 11px 13px;
            margin-top: 6px;
            border: 1px solid #d0d5dd;
            border-radius: 8px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            background: white;
            color: #24352f;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: 2px solid #b8dfc8;
            border-color: #27845a;
        }

        /* Alerts */
        .alert {
            max-width: 800px;
            margin: 18px auto;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-success {
            background: #e7f8ed;
            color: #176b37;
            border: 1px solid #c2ebd1;
        }

        .alert-danger {
            background: #fff0f0;
            color: #c62828;
            border: 1px solid #ffd4d4;
        }

        .alert-warning {
            background: #fff9e6;
            color: #9c6c00;
            border: 1px solid #ffe8a1;
        }

        .alert-info {
            background: #eef6ff;
            color: #155799;
            border: 1px solid #bee0ff;
        }

        .error-text {
            color: #c62828;
            font-size: 13px;
            margin-top: 4px;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-verified {
            background: #e7f8ed;
            color: #176b37;
            border: 1px solid #b6e9c6;
        }

        .badge-pending {
            background: #fff4e5;
            color: #b76e00;
            border: 1px solid #ffdca8;
        }

        .badge-suspended {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .badge-admin {
            background: #ede7f6;
            color: #512da8;
            border: 1px solid #d1c4e9;
        }

        .badge-available {
            background: #e7f8ed;
            color: #176b37;
        }

        .badge-borrowed {
            background: #e8f0fe;
            color: #1a73e8;
        }

        .badge-overdue {
            background: #fde8e8;
            color: #d93025;
            font-weight: bold;
        }

        .badge-returned {
            background: #f1f3f4;
            color: #5f6368;
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            text-align: left;
        }

        th {
            background: #f4f7f5;
            padding: 12px;
            font-weight: 600;
            color: #34483d;
            border-bottom: 2px solid #e0e7e3;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eef2f0;
            color: #2c3e35;
        }

        tr:hover td {
            background: #fbfdfc;
        }

        /* Grid */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e5ebe7;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #27845a;
            margin-bottom: 6px;
        }

        .stat-label {
            font-size: 13px;
            color: #667085;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #e5ebe7;
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .tab {
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            color: #667085;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            white-space: nowrap;
        }

        .tab:hover {
            color: #27845a;
        }

        .tab.active {
            color: #27845a;
            border-bottom-color: #27845a;
            background: #f0f8f3;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }

        footer {
            background: #eaf2ed;
            text-align: center;
            padding: 25px;
            color: #617168;
            font-size: 14px;
            line-height: 1.7;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 5%;
                flex-wrap: wrap;
                gap: 12px;
            }

            .grid-2, .grid-3, .grid-4 {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 18px;
                margin: 15px 12px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <header>
        <a href="{{ url('/') }}" class="logo">BorrowBox.</a>

        <nav>
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/items') }}">Browse Items</a>
            <a href="{{ route('items.create') }}" class="add-item">+ Add Item</a>

            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('history') }}">History</a>
                <a href="{{ route('notifications.index') }}" class="nav-bell" title="Notifications">
                    🔔
                    @php
                        $unreadCount = auth()->user()->unreadNotifications->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="nav-badge">{{ $unreadCount }}</span>
                    @endif
                </a>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" style="color: #512da8; font-weight: bold;">
                        🛡️ Admin
                    </a>
                @endif

                <a href="{{ route('profile.show') }}" style="font-size: 14px; color: #27845a; font-weight: bold; display: inline-flex; align-items: center; gap: 5px;">
                    👤 {{ auth()->user()->name }}
                    @if (auth()->user()->isVerified())
                        <span class="badge badge-verified" style="font-size: 10px; padding: 2px 6px;">✓</span>
                    @endif
                </a>

                <form action="{{ route('logout') }}" method="POST" style="display:inline; margin: 0;">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:#c62828; padding:0; font-size:14px; cursor:pointer; font-weight:bold;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </nav>
    </header>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    @auth
        @if (!auth()->user()->isVerified())
            <div class="alert alert-warning" style="text-align: center;">
                ⚠️ <strong>Account Not Verified:</strong> Your student verification is pending approval. You need a verified student account to list items or send borrow requests.
                <a href="{{ route('profile.show') }}" style="color: #9c6c00; font-weight: bold; text-decoration: underline; margin-left: 6px;">Check Verification Status</a>
            </div>
        @endif
    @endauth

    <main>
        @yield('content')
    </main>

    <footer>
        <p>BorrowBox — Created by Sadia, Afrin, Maliha.</p>
        <p>Share resources, support students.</p>
    </footer>

    @yield('scripts')
</body>
</html>

