@props(['id','title'=>''])
<div id="{{ $id }}" class="modal" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" hidden>
    <div style="display:flex; align-items:center; justify-content:space-between; padding:.75rem 1rem; border-bottom:1px solid #eee;">
        <h2 id="{{ $id }}-title" style="margin:0; font-size:1.1rem;">{{ $title }}</h2>
        <button type="button" class="btn" data-close aria-label="Close">✕</button>
    </div>
    <div style="padding:1rem;">
        {{ $slot }}
    </div>
</div>
<div class="modal-backdrop" data-backdrop-for="{{ $id }}" hidden></div>