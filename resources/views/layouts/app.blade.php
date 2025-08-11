<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Todo App - Laravel Livewire' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f5f5f5; min-height: 100vh; color: #333; }
        .container { max-width: 900px; margin: 0 auto; padding: 2rem 1rem; }
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: all 0.2s; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-primary:hover { background: #2563eb; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background: #d1d5db; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        .input { width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; }
        .input:focus { outline: none; border-color: #3b82f6; }
        .input.error { border-color: #ef4444; }
        .flex { display: flex; }
        .flex-1 { flex: 1; }
        .gap-3 { gap: 0.75rem; }
        .gap-2 { gap: 0.5rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-6 { margin-top: 1.5rem; }
        .mt-8 { margin-top: 2rem; }
        .pt-6 { padding-top: 1.5rem; }
        .p-4 { padding: 1rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .text-center { text-align: center; }
        .text-red { color: #ef4444; }
        .text-gray { color: #6b7280; }
        .text-blue { color: #3b82f6; }
        .text-green { color: #10b981; }
        .text-yellow { color: #f59e0b; }
        .text-black { color: #000; }
        .text-sm { font-size: 0.875rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-4xl { font-size: 2.25rem; }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .bg-gray-50 { background: #f9fafb; }
        .bg-blue-50 { background: #eff6ff; }
        .bg-green-50 { background: #ecfdf5; }
        .bg-yellow-50 { background: #fffbeb; }
        .bg-red-50 { background: #fef2f2; }
        .bg-green-100 { background: #dcfce7; }
        .border { border: 1px solid #e5e7eb; }
        .border-t { border-top: 1px solid #e5e7eb; }
        .border-green { border-color: #10b981; }
        .rounded { border-radius: 0.5rem; }
        .rounded-lg { border-radius: 0.75rem; }
        .hidden { display: none !important; }
        .space-y-3 > * + * { margin-top: 0.75rem; }
        .grid { display: grid; }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .relative { position: relative; }
        .fixed { position: fixed; }
        .inset-0 { top: 0; left: 0; right: 0; bottom: 0; }
        .z-50 { z-index: 50; }
        .bg-overlay { background: rgba(0,0,0,0.5); }
        .shadow { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .transition { transition: all 0.2s; }
        .hover-bg:hover { background: #f3f4f6; }
        .line-through { text-decoration: line-through; }
        .opacity-50 { opacity: 0.5; }
        .cursor-not-allowed { cursor: not-allowed; }
        .cursor-pointer { cursor: pointer; }
        .todo-item { display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; transition: all 0.2s; }
        .todo-item:hover { background: #f9fafb; }
        .checkbox { width: 1.25rem; height: 1.25rem; }
        .icon-btn { padding: 0.5rem; border: none; background: none; cursor: pointer; border-radius: 0.5rem; transition: all 0.2s; }
        .icon-btn:hover { background: #f3f4f6; }
        .edit-actions { display: flex; gap: 0.5rem; }
        .modal { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: white; border-radius: 0.75rem; padding: 1.5rem; max-width: 400px; margin: 1rem; }
        .alert { padding: 1rem; border-radius: 0.5rem; position: relative; }
        .alert-success { background: #dcfce7; border: 1px solid #10b981; color: #065f46; }
        .close-btn { position: absolute; top: 0.75rem; right: 0.75rem; background: none; border: none; cursor: pointer; font-size: 1.25rem; }
        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .card { padding: 1rem; }
            .flex-mobile { flex-direction: column; }
            .grid-3 { grid-template-columns: 1fr; gap: 1rem; }
        }
    </style>
    @livewireStyles
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-4xl font-bold text-blue mb-2">📝 Todo App</h1>
            <p class="text-gray">Manage your tasks efficiently with Laravel Livewire</p>
        </div>

        <!-- Main Content -->
        <div class="card">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>
</html>