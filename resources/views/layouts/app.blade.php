<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; }
        .container { max-width: 1100px; margin: 0 auto; padding: 1rem; }
        .btn { padding: .5rem .75rem; border: 1px solid #ddd; background: #f7f7f7; cursor: pointer; }
        .btn.primary { background: #2563eb; color: white; border-color: #1d4ed8; }
        .btn.danger { background: #dc2626; color: white; border-color: #b91c1c; }
        .input, select { padding: .45rem .5rem; border: 1px solid #ccc; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .5rem; border-bottom: 1px solid #eee; }
        th { text-align: left; }
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.45); display: none; }
        .modal { background: white; width: 100%; max-width: 640px; margin: 5vh auto; border-radius: 8px; overflow: hidden; }
        .modal.open + .modal-backdrop, .modal-backdrop.open { display: block; }
        .row-edit { background: #fffbe6; }
        .error { color: #b91c1c; font-size: .85rem; }
        .toast { position: fixed; right: 1rem; bottom: 1rem; background: #111; color: #fff; padding: .75rem 1rem; border-radius: 6px; opacity: 0; transform: translateY(10px); transition: all .2s; }
        .toast.show { opacity: 1; transform: translateY(0); }
        .badge { display:inline-block; padding: .1rem .4rem; border-radius: 4px; background:#eef; }
    </style>
</head>
<body>
    <div class="container">
        {{ $slot ?? '' }}
        @yield('content')
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>
</body>
</html>