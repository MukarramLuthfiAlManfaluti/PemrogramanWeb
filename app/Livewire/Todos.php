<?php

namespace App\Livewire;

require_once __DIR__ . '/../Models/Todo.php';

use App\Models\Todo;

class Todos
{
    public $newTodo = '';
    public $search = '';
    public $filter = 'all';
    public $currentPage = 1;
    public $perPage = 5;
    public $showModal = false;
    public $todoToDelete = null;

    protected $rules = [
        'newTodo' => 'required|max:255',
    ];

    public function mount()
    {
        // Component initialization
    }

    public function addTodo()
    {
        $this->validate();

        Todo::create([
            'title' => trim($this->newTodo),
            'completed' => false,
        ]);

        $this->newTodo = '';
        session()->flash('message', 'Todo added successfully!');
    }

    public function toggleTodo($id)
    {
        $todo = Todo::find($id);
        if ($todo) {
            $todo->update(['completed' => !$todo->completed]);
            $message = $todo->completed ? 'Todo marked as completed!' : 'Todo marked as pending!';
            session()->flash('message', $message);
        }
    }

    public function updateTodo($id, $title)
    {
        $todo = Todo::find($id);
        if ($todo && trim($title)) {
            $todo->update(['title' => trim($title)]);
            session()->flash('message', 'Todo updated successfully!');
        }
    }

    public function showDeleteModal($id)
    {
        $this->todoToDelete = $id;
        $this->showModal = true;
    }

    public function hideDeleteModal()
    {
        $this->showModal = false;
        $this->todoToDelete = null;
    }

    public function confirmDelete()
    {
        if ($this->todoToDelete) {
            $todo = Todo::find($this->todoToDelete);
            if ($todo) {
                $todo->delete();
                session()->flash('message', 'Todo deleted successfully!');
            }
        }
        $this->hideDeleteModal();
    }

    public function getTodosProperty()
    {
        $todos = Todo::all();
        
        // Apply search filter
        if ($this->search) {
            $todos = array_filter($todos, function($todo) {
                return stripos($todo->title, $this->search) !== false;
            });
        }

        // Apply status filter
        if ($this->filter === 'pending') {
            $todos = array_filter($todos, function($todo) {
                return !$todo->completed;
            });
        } elseif ($this->filter === 'completed') {
            $todos = array_filter($todos, function($todo) {
                return $todo->completed;
            });
        }

        return array_values($todos);
    }

    public function getPaginatedTodosProperty()
    {
        $todos = $this->getTodosProperty();
        $start = ($this->currentPage - 1) * $this->perPage;
        return array_slice($todos, $start, $this->perPage);
    }

    public function getTotalPagesProperty()
    {
        return ceil(count($this->getTodosProperty()) / $this->perPage);
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->currentPage = 1;
    }

    public function changePage($page)
    {
        $this->currentPage = $page;
    }

    public function getStatsProperty()
    {
        $allTodos = Todo::all();
        return [
            'total' => count($allTodos),
            'pending' => count(array_filter($allTodos, function($t) { return !$t->completed; })),
            'completed' => count(array_filter($allTodos, function($t) { return $t->completed; }))
        ];
    }

    public function render()
    {
        return view('livewire.todos', [
            'todos' => $this->getPaginatedTodosProperty(),
            'stats' => $this->getStatsProperty(),
            'totalPages' => $this->getTotalPagesProperty(),
        ]);
    }

    protected function validate()
    {
        if (empty(trim($this->newTodo))) {
            throw new \Exception('Please enter a todo title');
        }
        if (strlen(trim($this->newTodo)) > 255) {
            throw new \Exception('Todo title must be less than 255 characters');
        }
    }
}

// Session flash message helper
if (!function_exists('session')) {
    function session() {
        return new class {
            public function flash($key, $message) {
                $_SESSION['flash'][$key] = $message;
            }
            public function get($key) {
                return $_SESSION['flash'][$key] ?? null;
            }
            public function forget($key) {
                unset($_SESSION['flash'][$key]);
            }
        };
    }
}

session_start();
?>