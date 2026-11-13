@extends('layouts.app')

@section('title', 'Register - BorrowBox')

@section('content')
<div class="container container-narrow">
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 class="page-title">Join BorrowBox</h1>
        <p class="page-subtitle">Create an account to start sharing and borrowing on campus</p>
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

    <form action="{{ route('register.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="name">Full Name</label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            value="{{ old('name') }}" 
            placeholder="e.g. Alex Johnson" 
            required 
            autofocus
        >

        <label for="email">Campus Email</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email') }}" 
            placeholder="e.g. alex@university.edu" 
            required
        >

        <label for="student_id">Student ID</label>
        <input 
            type="text" 
            id="student_id" 
            name="student_id" 
            value="{{ old('student_id') }}" 
            placeholder="e.g. 2024-12345" 
            required
        >

        <label for="phone">Phone Number <span style="font-weight: normal; color: #667085;">(optional)</span></label>
        <input 
            type="text" 
            id="phone" 
            name="phone" 
            value="{{ old('phone') }}" 
            placeholder="e.g. +880 1712-345678"
        >

        <label for="id_card_image">Student ID Card Photo <span style="font-weight: normal; color: #667085;">(optional now, helps quick admin verification)</span></label>
        <input 
            type="file" 
            id="id_card_image" 
            name="id_card_image" 
            accept="image/*"
        >

        <label for="password">Password (min 6 characters)</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Choose a strong password" 
            required
        >

        <label for="password_confirmation">Confirm Password</label>
        <input 
            type="password" 
            id="password_confirmation" 
            name="password_confirmation" 
            placeholder="Re-enter your password" 
            required
        >

        <div style="margin-top: 18px; padding: 12px; background: #f0f8f3; border-radius: 8px; font-size: 13px; color: #267653; line-height: 1.5;">
            ℹ️ <strong>Campus Safety Notice:</strong> All student accounts must be verified by an admin with a valid student ID before listing items or requesting borrows.
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 22px; padding: 13px;">
            Create Student Account
        </button>
    </form>

    <div style="text-align: center; margin-top: 24px; font-size: 14px; color: #667085;">
        Already registered? 
        <a href="{{ route('login') }}" style="color: #27845a; font-weight: bold; text-decoration: none;">
            Log in here
        </a>
    </div>
</div>
@endsection

