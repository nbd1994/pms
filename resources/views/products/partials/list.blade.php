<table aria-label="Products">
    <thead>
        <tr>
            <th style="width:24px;"></th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Status</th>
            <th style="width:190px;">Actions</th>
        </tr>
    </thead>
    <tbody>
    @forelse($products as $p)
        <x-product-row :product="$p" />
    @empty
        <tr><td colspan="7">No products found.</td></tr>
    @endforelse
    </tbody>
</table>

@if($products->hasMorePages())
    <div id="listMeta" data-next-page="{{ $products->currentPage()+1 }}" data-last-page="{{ $products->lastPage() }}"></div>
@else
    <div id="listMeta" data-next-page="" data-last-page="{{ $products->lastPage() }}"></div>
@endif