@extends('layouts.app')

@section('title', 'Login - BorrowBox')

@section('content')
<div class="container container-narrow">
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 class="page-title">Welcome Back</h1>
        <p class="page-subtitle">Log in to borrow and share items with your campus peers</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-top: 0;">
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf

        <label for="email">Campus Email</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email') }}" 
            placeholder="e.g. student@borrowbox.test" 
            required 
            autofocus
        >

        <label for="password">Password</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Enter your password" 
            required
        >

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px;">
            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal; margin-top: 0; cursor: pointer;">
                <input type="checkbox" name="remember" style="width: auto; margin-top: 0;"> Remember me
            </label>
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 22px; padding: 13px;">
            Log In
        </button>
    </form>

    <div style="text-align: center; margin-top: 24px; font-size: 14px; color: #667085;">
        Don't have an account yet? 
        <a href="{{ route('register') }}" style="color: #27845a; font-weight: bold; text-decoration: none;">
            Register here
        </a>
    </div>
</div>
@endsection

