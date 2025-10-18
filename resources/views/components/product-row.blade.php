@props(['product'])

@php
    $p = $product;
@endphp

<tr data-id="{{ $p->id }}">
    <td><button class="btn btn-inline" data-inline-edit>Edit</button></td>
    <td data-view="name">{{ $p->name }}</td>
    <td data-view="price">{{ number_format($p->price, 2) }}</td>
    <td data-view="category">{{ optional($p->category)->name }}</td>
    <td data-view="stock">{{ $p->stock }}</td>
    <td data-view="status"><span class="badge">{{ $p->status }}</span></td>
    <td>
        <div data-actions>
            <button class="btn" data-edit>Open</button>
            <button class="btn danger" data-delete>Delete</button>
        </div>
        <form data-inline-form style="display:none;">
            <input name="name" class="input" value="{{ $p->name }}" />
            <input name="price" class="input" type="number" step="0.01" min="0" value="{{ $p->price }}" />
            <select name="category_id" class="input">
                @foreach(\App\Models\Category::orderBy('name')->get() as $c)
                    <option value="{{ $c->id }}" @selected($c->id===$p->category_id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <input name="stock" class="input" type="number" step="1" min="0" value="{{ $p->stock }}" />
            <select name="status" class="input">
                <option value="Active" @selected($p->status==='Active')>Active</option>
                <option value="Inactive" @selected($p->status==='Inactive')>Inactive</option>
            </select>
            <div style="display:inline-flex; gap:.25rem;">
                <button class="btn primary" data-inline-save>Save</button>
                <button class="btn" data-inline-cancel>Cancel</button>
            </div>
            <div class="error" data-inline-error></div>
        </form>
    </td>
</tr>