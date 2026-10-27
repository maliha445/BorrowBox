
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item | BorrowBox</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            padding: 40px 16px;
            color: #253858;
        }

        .container {
            max-width: 550px;
            margin: auto;
            background: white;
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 4px 20px #00000010;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #27845a;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        h1 {
            color: #253858;
            text-align: center;
            margin-bottom: 12px;
        }

        .subtitle {
            color: #667085;
            text-align: center;
            margin-bottom: 22px;
            line-height: 1.5;
        }

        label {
            display: block;
            margin-top: 16px;
            font-weight: bold;
            font-size: 14px;
        }

        input,
        textarea,
        select {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-top: 7px;
            border: 1px solid #d0d5dd;
            border-radius: 8px;
            font-size: 15px;
            font-family: Arial, sans-serif;
            background: white;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: 2px solid #b8dfc8;
            border-color: #27845a;
        }

        button {
            width: 100%;
            margin-top: 24px;
            padding: 14px;
            background: #27845a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #1e6846;
        }

        .success {
            padding: 12px;
            margin-bottom: 18px;
            background: #e7f8ed;
            color: #176b37;
            border-radius: 8px;
            line-height: 1.5;
        }

        .error {
            color: #c62828;
            font-size: 13px;
            margin-top: 5px;
        }

        .error-box {
            padding: 12px;
            margin-bottom: 15px;
            background: #fff0f0;
            border-radius: 8px;
        }

        .reward-note {
            margin-top: 15px;
            padding: 12px;
            background: #f0f8f3;
            color: #267653;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
        }

        footer {
            text-align: center;
            color: #8a9490;
            font-size: 12px;
            margin-top: 22px;
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 12px;
            }

            .container {
                padding: 22px 18px;
            }

            h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <a href="{{ url('/') }}" class="back-link">
        &larr; Back to Home
    </a>

    <h1>Add an Item</h1>

    <p class="subtitle">
        Share something useful with your fellow students!
    </p>

    @if (session('success'))
        <div class="success">
            {{ session('success') }}
            <br>
            Your item has been saved successfully.
            <br>
            <a href="{{ url('/') }}"
               style="color:#176b37; font-weight:bold;">
                Return to Homepage
            </a>
        </div>
    @endif

    @if ($errors->any())
        <div class="error-box">
            <strong>Please correct the following errors:</strong>

            @foreach ($errors->all() as $error)
                <div class="error">{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('items.store') }}" method="POST">
        @csrf

        <label for="item_name">Item Name</label>
        <input
            type="text"
            id="item_name"
            name="item_name"
            value="{{ old('item_name') }}"
            placeholder="e.g. Scientific Calculator"
            required
        >
        @error('item_name')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            placeholder="Describe your item..."
            required
        >{{ old('description') }}</textarea>
        @error('description')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">Select a category</option>
            <option value="Books" {{ old('category') == 'Books' ? 'selected' : '' }}>📚 Books</option>
            <option value="Calculators" {{ old('category') == 'Calculators' ? 'selected' : '' }}>🧮 Calculators</option>
            <option value="Electronics" {{ old('category') == 'Electronics' ? 'selected' : '' }}>💻 Electronics</option>
            <option value="Study Notes" {{ old('category') == 'Study Notes' ? 'selected' : '' }}>📝 Study Notes</option>
            <option value="Lab Equipment" {{ old('category') == 'Lab Equipment' ? 'selected' : '' }}>🔬 Lab Equipment</option>
            <option value="Sports Items" {{ old('category') == 'Sports Items' ? 'selected' : '' }}>⚽ Sports Items</option>
            <option value="Stationery" {{ old('category') == 'Stationery' ? 'selected' : '' }}>✏️ Stationery</option>
            <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>🎒 Other</option>
        </select>
        @error('category')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="condition">Condition</label>
        <select id="condition" name="condition">
            <option value="">Select condition</option>
            <option value="New" {{ old('condition') == 'New' ? 'selected' : '' }}>New</option>
            <option value="Good" {{ old('condition') == 'Good' ? 'selected' : '' }}>Good</option>
            <option value="Used" {{ old('condition') == 'Used' ? 'selected' : '' }}>Used</option>
        </select>

        <label for="owner_name">Owner Name</label>
        <input
            type="text"
            id="owner_name"
            name="owner_name"
            value="{{ old('owner_name', auth()->user()?->name ?? '') }}"
            placeholder="Enter your name"
            required
        >
        @error('owner_name')
            <div class="error">{{ $message }}</div>
        @enderror

        <div class="reward-note">
             Listed item reward: 100 coins
        </div>

        <button type="submit">
            Submit Item · Earn 100 Coins
        </button>
    </form>

    <footer>
        BorrowBox - created by Sadia,Afrin,Maliha 
    </footer>

</div>

</body>
</html>
