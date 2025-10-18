@extends('layouts.app')

@section('content')
<h1>Products</h1>

<div style="display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; margin:.75rem 0 1rem;">
    <select id="filterCategory" class="input">
        <option value="">All Categories</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
    </select>

    <input id="searchInput" class="input" type="search" placeholder="Search by name..." />

    <button data-sort="name" class="btn" id="sortName">Sort: Name</button>
    <button data-sort="price" class="btn" id="sortPrice">Sort: Price</button>

    <button id="btnNew" class="btn primary" style="margin-left:auto;">+ New Product</button>
</div>

<div id="productList" aria-live="polite"></div>

<div style="text-align:center; margin:1rem 0;">
    <button id="btnLoadMore" class="btn">Load More</button>
</div>

<x-modal id="productModal" title="Product">
    <form id="productForm" novalidate>
        <input type="hidden" name="id" />
        <div style="display:grid; gap:.5rem;">
            <label>Name
                <input class="input" name="name" required />
                <div class="error" data-error-for="name"></div>
            </label>
            <label>Price
                <input class="input" name="price" type="number" step="0.01" min="0" required />
                <div class="error" data-error-for="price"></div>
            </label>
            <label>Description
                <textarea class="input" name="description" rows="3"></textarea>
                <div class="error" data-error-for="description"></div>
            </label>
            <label>Category
                <select class="input" name="category_id" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <div class="error" data-error-for="category_id"></div>
            </label>
            <label>Stock
                <input class="input" name="stock" type="number" step="1" min="0" required />
                <div class="error" data-error-for="stock"></div>
            </label>
            <label>Status
                <select class="input" name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                <div class="error" data-error-for="status"></div>
            </label>
        </div>
        <div style="display:flex; gap:.5rem; justify-content:flex-end; margin-top:1rem;">
            <button type="button" class="btn" data-close>Cancel</button>
            <button type="submit" class="btn primary">Save</button>
        </div>
    </form>
</x-modal>

<x-modal id="confirmModal" title="Confirm delete">
    <p>Are you sure you want to delete this product?</p>
    <div style="display:flex; gap:.5rem; justify-content:flex-end; margin-top:1rem;">
        <button type="button" class="btn" data-close>Cancel</button>
        <button type="button" class="btn danger" id="confirmDeleteBtn">Delete</button>
    </div>
</x-modal>

<script>
    window.__CATEGORIES__ = @json($categories->map(fn($c)=>['id'=>$c->id,'name'=>$c->name]));
</script>
@endsection