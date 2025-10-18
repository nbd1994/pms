@props(['product'])

@php $p = $product; @endphp

<tr data-id="{{ $p->id }}">
    <td><button class="btn btn-inline" data-inline-edit>Edit</button></td>

    <td>
        <span class="cell-view" data-view="name">{{ $p->name }}</span>
        <input class="input cell-edit" name="name" form="inline-form-{{ $p->id }}" value="{{ $p->name }}" />
    </td>

    <td>
        <span class="cell-view" data-view="price">{{ number_format($p->price, 2) }}</span>
        <input class="input cell-edit" name="price" form="inline-form-{{ $p->id }}" type="number" step="0.01" min="0" value="{{ $p->price }}" />
    </td>

    <td>
        <span class="cell-view" data-view="category">{{ optional($p->category)->name }}</span>
        <select class="input cell-edit" name="category_id" form="inline-form-{{ $p->id }}">
            @foreach(\App\Models\Category::orderBy('name')->get() as $c)
                <option value="{{ $c->id }}" @selected($c->id===$p->category_id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </td>

    <td>
        <span class="cell-view" data-view="stock">{{ $p->stock }}</span>
        <input class="input cell-edit" name="stock" form="inline-form-{{ $p->id }}" type="number" step="1" min="0" value="{{ $p->stock }}" />
    </td>

    <td>
        <span class="cell-view" data-view="status"><span class="badge">{{ $p->status }}</span></span>
        <select class="input cell-edit" name="status" form="inline-form-{{ $p->id }}">
            <option value="Active" @selected($p->status==='Active')>Active</option>
            <option value="Inactive" @selected($p->status==='Inactive')>Inactive</option>
        </select>
    </td>

    <td>
        <div class="actions-view" data-actions>
            <button class="btn" data-edit>Open</button>
            <button class="btn danger" data-delete>Delete</button>
        </div>

        <div class="actions-edit">
            <form id="inline-form-{{ $p->id }}" data-inline-form></form>
            <button class="btn primary" data-inline-save>Save</button>
            <button class="btn" data-inline-cancel>Cancel</button>
            <div class="error" data-inline-error></div>
        </div>
    </td>
</tr>