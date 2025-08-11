<?php

namespace App\Models;

class Todo
{
    protected $fillable = ['title', 'completed'];
    protected $casts = [
        'completed' => 'boolean',
    ];

    private static $todos = [];
    private static $nextId = 1;

    public $id;
    public $title;
    public $completed;
    public $created_at;
    public $updated_at;

    public function __construct($attributes = [])
    {
        $this->id = self::$nextId++;
        $this->title = $attributes['title'] ?? '';
        $this->completed = $attributes['completed'] ?? false;
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public static function create($attributes)
    {
        $todo = new self($attributes);
        self::$todos[] = $todo;
        return $todo;
    }

    public static function all()
    {
        return array_reverse(self::$todos);
    }

    public static function find($id)
    {
        foreach (self::$todos as $todo) {
            if ($todo->id == $id) {
                return $todo;
            }
        }
        return null;
    }

    public function update($attributes)
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->$key = $value;
            }
        }
        $this->updated_at = date('Y-m-d H:i:s');
        return $this;
    }

    public function delete()
    {
        $index = array_search($this, self::$todos);
        if ($index !== false) {
            array_splice(self::$todos, $index, 1);
            return true;
        }
        return false;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'completed' => $this->completed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

// Initialize with sample data
Todo::create(['title' => 'Learn Laravel Livewire', 'completed' => true]);
Todo::create(['title' => 'Build a Todo App', 'completed' => false]);
Todo::create(['title' => 'Master Tailwind CSS', 'completed' => false]);
Todo::create(['title' => 'Deploy to Production', 'completed' => false]);
?>