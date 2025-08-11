<?php
/*
 * Laravel Livewire Demo - Todo App
 * This file demonstrates the Laravel Livewire structure and functionality
 * for the Todo application without requiring the full Laravel framework.
 */

// Simple template engine to mimic Blade
function view($template, $data = []) {
    extract($data);
    
    // Simulate Blade directives
    $content = file_get_contents(__DIR__ . '/resources/views/' . str_replace('.', '/', $template) . '.blade.php');
    
    // Replace Blade directives with PHP
    $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?php echo htmlspecialchars($1); ?>', $content);
    $content = preg_replace('/@if\s*\((.+?)\)/', '<?php if($1): ?>', $content);
    $content = preg_replace('/@endif/', '<?php endif; ?>', $content);
    $content = preg_replace('/@else/', '<?php else: ?>', $content);
    $content = preg_replace('/@forelse\s*\((.+?)\)/', '<?php if(count($1) > 0): foreach($1 as $todo): ?>', $content);
    $content = preg_replace('/@empty/', '<?php endforeach; else: ?>', $content);
    $content = preg_replace('/@endforelse/', '<?php endif; ?>', $content);
    $content = preg_replace('/@for\s*\((.+?)\)/', '<?php for($1): ?>', $content);
    $content = preg_replace('/@endfor/', '<?php endfor; ?>', $content);
    $content = preg_replace('/wire:model="([^"]+)"/', 'name="$1" value="<?php echo isset($$1) ? htmlspecialchars($$1) : \'\'; ?>"', $content);
    $content = preg_replace('/wire:click="([^"]+)"/', 'onclick="livewireCall(\'$1\')"', $content);
    
    // Create temporary file
    $tempFile = sys_get_temp_dir() . '/' . uniqid() . '.php';
    file_put_contents($tempFile, $content);
    
    ob_start();
    include $tempFile;
    $output = ob_get_clean();
    unlink($tempFile);
    
    return $output;
}

// Load the Livewire component
require_once __DIR__ . '/app/Livewire/Todos.php';

// Initialize the component
$todosComponent = new App\Livewire\Todos();

// Handle AJAX requests (simulate Livewire)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livewire_action'])) {
    session_start();
    
    $action = $_POST['livewire_action'];
    $params = json_decode($_POST['params'] ?? '[]', true);
    
    try {
        switch ($action) {
            case 'addTodo':
                $todosComponent->newTodo = $_POST['newTodo'] ?? '';
                $todosComponent->addTodo();
                break;
            case 'toggleTodo':
                $todosComponent->toggleTodo($params[0]);
                break;
            case 'updateTodo':
                $todosComponent->updateTodo($params[0], $params[1]);
                break;
            case 'showDeleteModal':
                $todosComponent->showDeleteModal($params[0]);
                break;
            case 'confirmDelete':
                $todosComponent->confirmDelete();
                break;
            case 'hideDeleteModal':
                $todosComponent->hideDeleteModal();
                break;
            case 'setFilter':
                $todosComponent->setFilter($params[0]);
                break;
            case 'changePage':
                $todosComponent->changePage($params[0]);
                break;
        }
        
        // Return updated component state
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => session()->get('message'),
            'component' => $todosComponent->render()
        ]);
        session()->forget('message');
        exit;
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

// Render the component
session_start();
$componentData = $todosComponent->render();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo App - Laravel Livewire Demo</title>
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
        .input { width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; }
        .input:focus { outline: none; border-color: #3b82f6; }
        .flex { display: flex; }
        .flex-1 { flex: 1; }
        .gap-3 { gap: 0.75rem; }
        .gap-2 { gap: 0.5rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .text-center { text-align: center; }
        .text-red { color: #ef4444; }
        .text-gray { color: #6b7280; }
        .text-blue { color: #3b82f6; }
        .text-sm { font-size: 0.875rem; }
        .text-4xl { font-size: 2.25rem; }
        .font-bold { font-weight: 700; }
        .alert { padding: 1rem; border-radius: 0.5rem; background: #dcfce7; border: 1px solid #10b981; color: #065f46; }
        .todo-item { display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; margin-bottom: 0.75rem; }
        .checkbox { width: 1.25rem; height: 1.25rem; }
        .icon-btn { padding: 0.5rem; border: none; background: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-4xl font-bold text-blue mb-2">📝 Todo App - Laravel Livewire Demo</h1>
            <p class="text-gray">Server-side rendering with PHP (Laravel Livewire structure)</p>
        </div>

        <div class="card">
            <!-- Flash Message -->
            <?php if (session()->get('message')): ?>
                <div class="mb-6">
                    <div class="alert">
                        <?php echo htmlspecialchars(session()->get('message')); ?>
                    </div>
                </div>
                <?php session()->forget('message'); ?>
            <?php endif; ?>

            <!-- Add Todo Form -->
            <div class="mb-6">
                <form onsubmit="return livewireSubmit(event, 'addTodo')">
                    <div class="flex gap-3">
                        <input 
                            name="newTodo"
                            type="text" 
                            placeholder="Enter a new todo..." 
                            class="flex-1 input"
                            required
                        >
                        <button type="submit" class="btn btn-primary">➕ Add Todo</button>
                    </div>
                </form>
            </div>

            <!-- Filter Buttons -->
            <div class="mb-6">
                <div class="flex gap-2">
                    <button onclick="livewireCall('setFilter', ['all'])" class="btn btn-primary">
                        All (<?php echo $componentData['stats']['total']; ?>)
                    </button>
                    <button onclick="livewireCall('setFilter', ['pending'])" class="btn btn-secondary">
                        Pending (<?php echo $componentData['stats']['pending']; ?>)
                    </button>
                    <button onclick="livewireCall('setFilter', ['completed'])" class="btn btn-secondary">
                        Completed (<?php echo $componentData['stats']['completed']; ?>)
                    </button>
                </div>
            </div>

            <!-- Todos List -->
            <div class="mb-6">
                <?php if (count($componentData['todos']) > 0): ?>
                    <?php foreach ($componentData['todos'] as $todo): ?>
                        <div class="todo-item">
                            <input 
                                type="checkbox" 
                                <?php echo $todo->completed ? 'checked' : ''; ?>
                                onchange="livewireCall('toggleTodo', [<?php echo $todo->id; ?>])"
                                class="checkbox"
                            >
                            <div class="flex-1">
                                <span style="<?php echo $todo->completed ? 'text-decoration: line-through; color: #6b7280;' : 'color: #000; font-weight: 500;'; ?>">
                                    <?php echo htmlspecialchars($todo->title); ?>
                                </span>
                            </div>
                            <div>
                                <button 
                                    onclick="editTodo(<?php echo $todo->id; ?>, '<?php echo addslashes($todo->title); ?>')"
                                    class="icon-btn"
                                    title="Edit todo"
                                >
                                    ✏️
                                </button>
                                <button 
                                    onclick="livewireCall('showDeleteModal', [<?php echo $todo->id; ?>])"
                                    class="icon-btn"
                                    title="Delete todo"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center" style="padding: 3rem 0;">
                        <div style="font-size: 2.25rem; margin-bottom: 1rem;">📋</div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">No todos found</h3>
                        <p style="color: #6b7280;">Add your first todo to get started!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Stats -->
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div style="background: #eff6ff; padding: 1rem; border-radius: 0.75rem; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6;"><?php echo $componentData['stats']['total']; ?></div>
                        <div style="color: #3b82f6; font-weight: 500;">Total Tasks</div>
                    </div>
                    <div style="background: #fffbeb; padding: 1rem; border-radius: 0.75rem; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;"><?php echo $componentData['stats']['pending']; ?></div>
                        <div style="color: #f59e0b; font-weight: 500;">Pending</div>
                    </div>
                    <div style="background: #ecfdf5; padding: 1rem; border-radius: 0.75rem; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;"><?php echo $componentData['stats']['completed']; ?></div>
                        <div style="color: #10b981; font-weight: 500;">Completed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function livewireCall(action, params = []) {
            const formData = new FormData();
            formData.append('livewire_action', action);
            formData.append('params', JSON.stringify(params));
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.message) {
                        alert(data.message);
                    }
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error occurred');
            });
        }

        function livewireSubmit(event, action) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('livewire_action', action);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error occurred');
            });
            
            return false;
        }

        function editTodo(id, currentTitle) {
            const newTitle = prompt('Edit todo:', currentTitle);
            if (newTitle !== null && newTitle.trim() !== '') {
                livewireCall('updateTodo', [id, newTitle.trim()]);
            }
        }
    </script>
</body>
</html>