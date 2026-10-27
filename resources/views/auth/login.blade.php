    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BorrowBox - Campus Item Sharing</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f8faf9;
            padding: 40px 16px;
            color: #24352f;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 480px;
            background: white;
            padding: 32px 28px;
            border-radius: 16px;
            box-shadow: 0 4px 20px #00000010;
            border: 1px solid #e5ebe7;
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

        .brand-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #267653;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 6px;
        }

        h1 {
            color: #253858;
            text-align: center;
            font-size: 25px;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #667085;
            text-align: center;
            margin-bottom: 24px;
            line-height: 1.5;
            font-size: 14px;
        }

        label {
            display: block;
            margin-top: 16px;
            font-weight: bold;
            font-size: 14px;
            color: #253858;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            padding: 12px 14px;
            margin-top: 7px;
            border: 1px solid #d0d5dd;
            border-radius: 8px;
            font-size: 15px;
            font-family: Arial, sans-serif;
            background: white;
            color: #24352f;
            transition: border-color 0.2s, outline 0.2s;
        }

        input:focus {
            outline: 2px solid #b8dfc8;
            border-color: #27845a;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            font-size: 13px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #4b5a52;
            user-select: none;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #27845a;
            cursor: pointer;
        }

        .forgot-link {
            color: #27845a;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        button[type="submit"] {
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
            transition: background 0.2s;
        }

        button[type="submit"]:hover {
            background: #1e6846;
        }

        .success {
            padding: 12px;
            margin-bottom: 18px;
            background: #e7f8ed;
            color: #176b37;
            border-radius: 8px;
            line-height: 1.5;
            font-size: 14px;
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
            color: #c62828;
            font-size: 14px;
        }

        .reward-note {
            margin-top: 20px;
            padding: 12px;
            background: #f0f8f3;
            color: #267653;
            border-radius: 8px;
            text-align: center;
            font-size: 13px;
        }

        .auth-switch {
            text-align: center;
            margin-top: 22px;
            font-size: 14px;
            color: #667085;
        }

        .auth-switch a {
            color: #27845a;
            font-weight: bold;
            text-decoration: none;
        }

        .auth-switch a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            color: #8a9490;
            font-size: 12px;
            margin-top: 24px;
            line-height: 1.5;
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 12px;
            }

            .container {
                padding: 22px 18px;
            }

            h1 {
                font-size: 23px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <a href="{{ url('/') }}" class="back-link">
        &larr; Back to Home
    </a>

    <div class="brand-header">
        <a href="{{ url('/') }}" class="logo">BorrowBox.</a>
        <h1>Welcome Back</h1>
        <p class="subtitle">
            Log in to borrow items or share with campus students.
        </p>
    </div>

    @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('status'))
        <div class="success">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="error-box">
            {{ session('error') }}
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

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf

        <label for="email">Student Email Address</label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="e.g. student@university.edu"
            required
            autocomplete="email"
            autofocus
        >
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
            autocomplete="current-password"
        >
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <div class="form-options">
            <label class="remember-me">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember me</span>
            </label>

            <a href="#" class="forgot-link">Forgot password?</a>
        </div>

        <button type="submit">
            Log In
        </button>

        <div class="reward-note">
            Share what you have · Borrow what you need
        </div>

        <div class="auth-switch">
            Don't have an account?
            <a href="{{ url('/register') }}">Register here</a>
        </div>
    </form>

    <footer>
        BorrowBox — Created by Sadia, Afrin, Maliha
    </footer>

</div>

</body>
</html>
