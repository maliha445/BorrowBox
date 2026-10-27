
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BorrowBox - Campus Item Sharing</title>

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
        }

        header {
            background: white;
            padding: 20px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5ebe7;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
            color: #267653;
        }

        nav {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        nav a {
            text-decoration: none;
            color: #34483d;
            font-size: 14px;
        }

        nav a:hover {
            color: #27845a;
        }

        nav .add-item {
            background: #27845a;
            color: white;
            padding: 11px 16px;
            border-radius: 8px;
        }

        nav .add-item:hover {
            background: #1e6846;
        }

        .hero {
            text-align: center;
            padding: 65px 20px 45px;
        }

        .hero h1 {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .hero h1 span {
            color: #27845a;
        }

        .hero p {
            color: #6b7971;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .search-box {
            max-width: 560px;
            margin: auto;
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            min-width: 0;
            padding: 14px;
            border: 1px solid #d8e2dc;
            border-radius: 8px;
            font-size: 14px;
        }

        button {
            background: #27845a;
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background: #1e6846;
        }

        .section {
            padding: 30px 7% 50px;
        }

        .section h2 {
            font-size: 25px;
            margin-bottom: 22px;
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .category {
            background: white;
            padding: 22px 10px;
            text-align: center;
            border: 1px solid #e5ebe7;
            border-radius: 10px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.2s ease;
        }

        .category:hover, .category.active-cat {
            border-color: #27845a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 132, 90, 0.15);
            background: #f0f8f3;
        }

        .category .icon {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .category p {
            font-size: 14px;
            margin: 0;
            font-weight: 500;
        }

        .items {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .item {
            background: white;
            border: 1px solid #e5ebe7;
            border-radius: 12px;
            overflow: hidden;
        }

        .item-image {
            background: #e8f2eb;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 55px;
        }

        .item-info {
            padding: 18px;
        }

        .item-info h3 {
            margin-bottom: 8px;
            font-size: 18px;
            overflow-wrap: anywhere;
        }

        .item-info p {
            color: #718078;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 12px;
            overflow-wrap: anywhere;
        }

        .available {
            color: #27845a;
            font-size: 13px;
        }

        .owner {
            display: block;
            color: #66756c;
            font-size: 13px;
            margin-top: 10px;
        }

        .empty-message {
            background: white;
            border: 1px solid #e5ebe7;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            color: #718078;
            grid-column: 1 / -1;
            line-height: 1.8;
        }

        .debug-info {
            text-align: center;
            color: #27845a;
            font-size: 13px;
            margin: 0 auto 20px;
        }

        footer {
            background: #eaf2ed;
            text-align: center;
            padding: 25px;
            color: #617168;
            font-size: 14px;
            line-height: 1.7;
        }

        @media (max-width: 650px) {
            header {
                padding: 18px 5%;
                flex-wrap: wrap;
                gap: 15px;
            }

            nav {
                gap: 12px;
            }

            nav a {
                font-size: 13px;
            }

            .hero h1 {
                font-size: 31px;
            }

            .categories {
                grid-template-columns: repeat(2, 1fr);
            }

            .items {
                grid-template-columns: 1fr;
            }

            .search-box {
                flex-wrap: wrap;
            }

            .search-box button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">BorrowBox.</div>

        <nav>
            <a href="{{ url('/') }}">Home</a>
            <a href="#categories">Categories</a>
            <a href="#items">Browse Items</a>
            <a href="{{ route('items.create') }}" class="add-item">
                + Add Item
            </a>
            @auth
                <span style="font-size: 14px; color: #27845a; font-weight: bold;"> {{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display:inline; margin: 0;">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:#c62828; padding:0; font-size:14px; cursor:pointer; font-weight:bold;">Logout</button>
                </form>
            @else
                <a href="{{ url('/login') }}">Login</a>
                <a href="{{ url('/register') }}">Register</a>
            @endauth
        </nav>
    </header>

    @if (session('success'))
        <div style="max-width: 600px; margin: 20px auto -20px; background: #e7f8ed; color: #176b37; padding: 12px 20px; border-radius: 8px; text-align: center; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <section class="hero">
        <h1>Borrow More. <span>Spend Less.</span></h1>

        <p>
            Share what you have. Borrow what you need.<br>
            A simple sharing community for university students.
        </p>

        <form class="search-box" action="{{ url('/') }}" method="GET">
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search books, calculators and more..."
            >
            <button type="submit">Search Items</button>
        </form>
    </section>

    <section class="section" id="categories">
        <h2>Explore Categories</h2>

        <div class="categories">
            <a href="{{ url('/?category=Books') }}" class="category {{ request('category') === 'Books' ? 'active-cat' : '' }}">
                <div class="icon">📚</div>
                <p>Books</p>
            </a>

            <a href="{{ url('/?category=Calculators') }}" class="category {{ request('category') === 'Calculators' ? 'active-cat' : '' }}">
                <div class="icon">🧮</div>
                <p>Calculators</p>
            </a>

            <a href="{{ url('/?category=Electronics') }}" class="category {{ request('category') === 'Electronics' ? 'active-cat' : '' }}">
                <div class="icon">💻</div>
                <p>Electronics</p>
            </a>

            <a href="{{ url('/?category=Study Notes') }}" class="category {{ request('category') === 'Study Notes' ? 'active-cat' : '' }}">
                <div class="icon">📝</div>
                <p>Study Notes</p>
            </a>

            <a href="{{ url('/?category=Lab Equipment') }}" class="category {{ request('category') === 'Lab Equipment' ? 'active-cat' : '' }}">
                <div class="icon">🔬</div>
                <p>Lab Equipment</p>
            </a>

            <a href="{{ url('/?category=Sports Items') }}" class="category {{ request('category') === 'Sports Items' ? 'active-cat' : '' }}">
                <div class="icon">⚽</div>
                <p>Sports Items</p>
            </a>

            <a href="{{ url('/?category=Stationery') }}" class="category {{ request('category') === 'Stationery' ? 'active-cat' : '' }}">
                <div class="icon">✏️</div>
                <p>Stationery</p>
            </a>

            <a href="{{ url('/?category=Other') }}" class="category {{ request('category') === 'Other' ? 'active-cat' : '' }}">
                <div class="icon">🎒</div>
                <p>Other Items</p>
            </a>
        </div>
    </section>

    <section class="section" id="items">

        <h2>
            @if (request()->filled('search'))
                Search Results for "{{ request('search') }}"
            @elseif (request()->filled('category'))
                Category: {{ request('category') }}
            @else
                Recently Shared Items
            @endif
        </h2>

        @if (request()->filled('search') || request()->filled('category'))
            <p class="debug-info">
                @if (request()->filled('search')) Keyword: "{{ request('search') }}" @endif
                @if (request()->filled('category')) Category: "{{ request('category') }}" @endif
                | Results found: {{ $items->count() }}
                &nbsp;·&nbsp;
                <a href="{{ url('/') }}" style="color: #27845a; font-weight: bold; text-decoration: underline;">Clear Filter</a>
            </p>
        @else
            <p class="debug-info">
                Total available items: {{ $items->count() }}
            </p>
        @endif

        <div class="items">

            @forelse ($items as $item)

                <div class="item">

                    <div class="item-image">
                        @php $cat = strtolower($item->category); @endphp
                        @if (str_contains($cat, 'book'))
                            📚
                        @elseif (str_contains($cat, 'calc'))
                            🧮
                        @elseif (str_contains($cat, 'electr'))
                            💻
                        @elseif (str_contains($cat, 'note') || str_contains($cat, 'study'))
                            📝
                        @elseif (str_contains($cat, 'lab'))
                            🔬
                        @elseif (str_contains($cat, 'sport'))
                            ⚽
                        @elseif (str_contains($cat, 'station'))
                            ✏️
                        @else
                            🎒
                        @endif
                    </div>

                    <div class="item-info">

                        <h3>{{ $item->item_name }}</h3>

                        <p>{{ $item->description }}</p>

                        <p>
                            <strong>Category:</strong>
                            {{ $item->category }}
                        </p>

                        @if ($item->condition)
                            <p>
                                <strong>Condition:</strong>
                                {{ $item->condition }}
                            </p>
                        @endif

                        <span class="available">
                            ● {{ $item->is_available ? 'Available' : 'Not Available' }}
                        </span>

                        <span class="owner">
                            Shared by: {{ $item->owner_name }}
                        </span>

                        <span class="owner">
                            🎁 Listing reward: {{ $item->reward_coins }} coins
                        </span>

                    </div>
                </div>

            @empty

                <div class="empty-message">
                    @if (request()->filled('search'))
                        <h3>No matching items found.</h3>
                        <p>Try another keyword, such as Calculator or Electronics.</p>
                    @else
                        <h3>No items shared yet.</h3>
                        <p>Be the first to share something with your fellow students!</p>
                    @endif

                    <p style="margin-top:15px;">
                        <a
                            href="{{ route('items.create') }}"
                            style="color:#27845a; font-weight:bold; text-decoration:none;"
                        >
                            + Add an Item
                        </a>
                    </p>
                </div>

            @endforelse

        </div>
    </section>

    <footer>
        <p>BorrowBox — Created by Sadia, Afrin, Maliha.</p>
        <p>Share resources, support students.</p>
    </footer>

</body>
</html>
