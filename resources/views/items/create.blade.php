@extends('layouts.app')

@section('title', 'Add an Item - BorrowBox')

@section('content')
<div class="container container-narrow">
    <a href="{{ route('items.index') }}" class="btn btn-sm btn-secondary" style="margin-bottom: 20px;">
        &larr; Back to Items
    </a>

    <h1 class="page-title" style="text-align: center; margin-bottom: 8px;">Add an Item</h1>
    <p class="page-subtitle" style="text-align: center; margin-bottom: 24px;">
        Share something useful with your fellow students!
    </p>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
            <br>
            <a href="{{ route('items.index') }}" style="color:#176b37; font-weight:bold;">
                View All Items
            </a>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul style="margin-left: 20px; margin-top: 6px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="item_name">Item Name</label>
        <input
            type="text"
            id="item_name"
            name="item_name"
            value="{{ old('item_name') }}"
            placeholder="e.g. Scientific Calculator Casio FX-991EX"
            required
        >

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            placeholder="Describe your item condition, edition, accessories included..."
            required
        >{{ old('description') }}</textarea>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">Select a category</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->icon }} {{ $cat->name }}
                </option>
            @endforeach
        </select>

        <label for="condition">Condition</label>
        <select id="condition" name="condition">
            <option value="">Select condition</option>
            <option value="New" {{ old('condition') == 'New' ? 'selected' : '' }}>New</option>
            <option value="Like New" {{ old('condition') == 'Like New' ? 'selected' : '' }}>Like New</option>
            <option value="Good" {{ old('condition') == 'Good' ? 'selected' : '' }}>Good</option>
            <option value="Fair / Used" {{ old('condition') == 'Fair / Used' ? 'selected' : '' }}>Fair / Used</option>
        </select>

        <label for="image">Item Photo <span style="font-weight: normal; color: #667085;">(recommended)</span></label>
        <input 
            type="file" 
            id="image" 
            name="image" 
            accept="image/*"
        >

        <label for="owner_name">Owner Name</label>
        <input
            type="text"
            id="owner_name"
            name="owner_name"
            value="{{ old('owner_name', auth()->user()?->name ?? $defaultOwner) }}"
            placeholder="Enter your name"
            required
        >

        <div style="margin-top: 18px; padding: 12px; background: #f0f8f3; color: #267653; border-radius: 8px; text-align: center; font-size: 14px;">
            🎁 Listed item reward: <strong>100 coins</strong>
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 22px; padding: 14px;">
            Submit Item &bull; Earn 100 Coins
        </button>
    </form>
</div>
@endsection
