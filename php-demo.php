<?php
session_start();

// Simple Todo class
class SimpleTodo {
    public $id;
    public $title;
    public $completed;
    
    public function __construct($id, $title, $completed = false) {
        $this->id = $id;
        $this->title = $title;
        $this->completed = $completed;
    }
}

// Initialize todos in session
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [
        new SimpleTodo(1, 'Learn Laravel Livewire', true),
        new SimpleTodo(2, 'Build a Todo App', false),
        new SimpleTodo(3, 'Master Tailwind CSS', false),
        new SimpleTodo(4, 'Deploy to Production', false),
    ];
    $_SESSION['next_id'] = 5;
}

// Handle form submissions
$message = '';
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                if (!empty(trim($_POST['title']))) {
                    $_SESSION['todos'][] = new SimpleTodo($_SESSION['next_id']++, trim($_POST['title']), false);
                    $message = 'Todo added successfully!';
                }
                break;
                
            case 'toggle':
                $id = (int)$_POST['id'];
                foreach ($_SESSION['todos'] as $todo) {
                    if ($todo->id === $id) {
                        $todo->completed = !$todo->completed;
                        $message = $todo->completed ? 'Todo marked as completed!' : 'Todo marked as pending!';
                        break;
                    }
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $title = trim($_POST['title']);
                if (!empty($title)) {
                    foreach ($_SESSION['todos'] as $todo) {
                        if ($todo->id === $id) {
                            $todo->title = $title;
                            $message = 'Todo updated successfully!';
                            break;
                        }
                    }
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                $_SESSION['todos'] = array_filter($_SESSION['todos'], function($todo) use ($id) {
                    return $todo->id !== $id;
                });
                $_SESSION['todos'] = array_values($_SESSION['todos']);
                $message = 'Todo deleted successfully!';
                break;
        }
    }
}

$todos = $_SESSION['todos'];
$stats = [
    'total' => count($todos),
    'pending' => count(array_filter($todos, function($t) { return !$t->completed; })),
    'completed' => count(array_filter($todos, function($t) { return $t->completed; }))
];
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
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }
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
        .text-green { color: #10b981; }
        .text-yellow { color: #f59e0b; }
        .text-sm { font-size: 0.875rem; }
        .text-4xl { font-size: 2.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }
        .alert { padding: 1rem; border-radius: 0.5rem; background: #dcfce7; border: 1px solid #10b981; color: #065f46; margin-bottom: 1.5rem; }
        .todo-item { display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; margin-bottom: 0.75rem; transition: all 0.2s; }
        .todo-item:hover { background: #f9fafb; }
        .checkbox { width: 1.25rem; height: 1.25rem; }
        .icon-btn { padding: 0.5rem; border: none; background: none; cursor: pointer; border-radius: 0.5rem; font-size: 1.25rem; }
        .icon-btn:hover { background: #f3f4f6; }
        .line-through { text-decoration: line-through; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; }
        .stat-card { padding: 1rem; border-radius: 0.75rem; text-align: center; }
        .edit-form { display: none; flex: 1; gap: 0.5rem; }
        .edit-form.active { display: flex; }
        .todo-title { flex: 1; }
        .todo-title.editing { display: none; }
        .hidden { display: none; }
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
            <?php if ($message): ?>
                <div class="alert"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <!-- Add Todo Form -->
            <div class="mb-6">
                <form method="POST" class="flex gap-3">
                    <input type="hidden" name="action" value="add">
                    <input 
                        name="title"
                        type="text" 
                        placeholder="Enter a new todo..." 
                        class="flex-1 input"
                        required
                    >
                    <button type="submit" class="btn btn-primary">➕ Add Todo</button>
                </form>
            </div>

            <!-- Stats -->
            <div class="mb-6">
                <div class="flex gap-2">
                    <div class="btn btn-primary">All (<?php echo $stats['total']; ?>)</div>
                    <div class="btn btn-secondary">Pending (<?php echo $stats['pending']; ?>)</div>
                    <div class="btn btn-secondary">Completed (<?php echo $stats['completed']; ?>)</div>
                </div>
            </div>

            <!-- Todos List -->
            <div class="mb-6">
                <?php if (count($todos) > 0): ?>
                    <?php foreach ($todos as $todo): ?>
                        <div class="todo-item" id="todo-<?php echo $todo->id; ?>">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?php echo $todo->id; ?>">
                                <input 
                                    type="checkbox" 
                                    <?php echo $todo->completed ? 'checked' : ''; ?>
                                    onchange="this.form.submit()"
                                    class="checkbox"
                                >
                            </form>
                            
                            <div class="todo-title <?php echo $todo->completed ? 'line-through text-gray' : 'text-black font-medium'; ?>" id="title-<?php echo $todo->id; ?>">
                                <?php echo htmlspecialchars($todo->title); ?>
                            </div>
                            
                            <form method="POST" class="edit-form" id="edit-<?php echo $todo->id; ?>">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?php echo $todo->id; ?>">
                                <input type="text" name="title" value="<?php echo htmlspecialchars($todo->title); ?>" class="input" style="flex: 1;">
                                <button type="submit" class="btn btn-primary btn-sm">✓ Save</button>
                                <button type="button" onclick="cancelEdit(<?php echo $todo->id; ?>)" class="btn btn-secondary btn-sm">✗ Cancel</button>
                            </form>
                            
                            <div>
                                <button 
                                    onclick="startEdit(<?php echo $todo->id; ?>)"
                                    class="icon-btn"
                                    title="Edit todo"
                                    id="edit-btn-<?php echo $todo->id; ?>"
                                >
                                    ✏️
                                </button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this todo?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $todo->id; ?>">
                                    <button 
                                        type="submit"
                                        class="icon-btn"
                                        title="Delete todo"
                                    >
                                        🗑️
                                    </button>
                                </form>
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

            <!-- Stats Dashboard -->
            <div class="stats-grid">
                <div class="stat-card" style="background: #eff6ff;">
                    <div class="text-2xl font-bold text-blue"><?php echo $stats['total']; ?></div>
                    <div class="text-blue font-medium">Total Tasks</div>
                </div>
                <div class="stat-card" style="background: #fffbeb;">
                    <div class="text-2xl font-bold text-yellow"><?php echo $stats['pending']; ?></div>
                    <div class="text-yellow font-medium">Pending</div>
                </div>
                <div class="stat-card" style="background: #ecfdf5;">
                    <div class="text-2xl font-bold text-green"><?php echo $stats['completed']; ?></div>
                    <div class="text-green font-medium">Completed</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function startEdit(id) {
            document.getElementById('title-' + id).style.display = 'none';
            document.getElementById('edit-btn-' + id).style.display = 'none';
            document.getElementById('edit-' + id).classList.add('active');
        }

        function cancelEdit(id) {
            document.getElementById('title-' + id).style.display = 'block';
            document.getElementById('edit-btn-' + id).style.display = 'inline-block';
            document.getElementById('edit-' + id).classList.remove('active');
        }
    </script>
</body>
</html>