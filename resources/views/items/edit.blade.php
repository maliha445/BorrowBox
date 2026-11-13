@extends('layouts.app')

@section('title', 'Edit Item - BorrowBox')

@section('content')
<div class="container container-narrow">
    <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-secondary" style="margin-bottom: 20px;">
        &larr; Back to Item Details
    </a>

    <h1 class="page-title" style="text-align: center; margin-bottom: 8px;">Edit Item Listing</h1>
    <p class="page-subtitle" style="text-align: center; margin-bottom: 24px;">
        Update your item information or change availability
    </p>

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

    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="item_name">Item Name</label>
        <input
            type="text"
            id="item_name"
            name="item_name"
            value="{{ old('item_name', $item->item_name) }}"
            required
        >

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            required
        >{{ old('description', $item->description) }}</textarea>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id || $item->category == $cat->name ? 'selected' : '' }}>
                    {{ $cat->icon }} {{ $cat->name }}
                </option>
            @endforeach
        </select>

        <label for="condition">Condition</label>
        <select id="condition" name="condition">
            <option value="New" {{ old('condition', $item->condition) == 'New' ? 'selected' : '' }}>New</option>
            <option value="Like New" {{ old('condition', $item->condition) == 'Like New' ? 'selected' : '' }}>Like New</option>
            <option value="Good" {{ old('condition', $item->condition) == 'Good' ? 'selected' : '' }}>Good</option>
            <option value="Fair / Used" {{ old('condition', $item->condition) == 'Fair / Used' ? 'selected' : '' }}>Fair / Used</option>
        </select>

        <label for="status">Availability Status</label>
        <select id="status" name="status">
            <option value="available" {{ old('status', $item->status) == 'available' ? 'selected' : '' }}>Available</option>
            <option value="borrowed" {{ old('status', $item->status) == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
            <option value="returned" {{ old('status', $item->status) == 'returned' ? 'selected' : '' }}>Returned / Idle</option>
            <option value="overdue" {{ old('status', $item->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
        </select>

        <label for="image">Replace Photo <span style="font-weight: normal; color: #667085;">(leave blank to keep current photo)</span></label>
        <input 
            type="file" 
            id="image" 
            name="image" 
            accept="image/*"
        >

        @if ($item->image)
            <div style="margin-top: 10px;">
                <p style="font-size: 12px; color: #667085; margin-bottom: 5px;">Current image:</p>
                <img src="{{ asset($item->image) }}" alt="Item image" style="max-height: 120px; border-radius: 8px; border: 1px solid #d0d5dd;">
            </div>
        @endif

        <button type="submit" class="btn" style="width: 100%; margin-top: 24px; padding: 14px;">
            Update Item
        </button>
    </form>
</div>
@endsection

