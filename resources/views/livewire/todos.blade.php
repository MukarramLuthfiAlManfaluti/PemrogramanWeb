<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-6">
            <div class="alert alert-success">
                {{ session('message') }}
                <button class="close-btn" onclick="this.parentElement.parentElement.style.display='none'">×</button>
            </div>
        </div>
    @endif

    <!-- Add Todo Form -->
    <div class="mb-6">
        <div class="flex gap-3">
            <input 
                wire:model="newTodo" 
                wire:keydown.enter="addTodo"
                type="text" 
                placeholder="Enter a new todo..." 
                class="flex-1 input @error('newTodo') error @enderror"
            >
            <button 
                wire:click="addTodo"
                class="btn btn-primary"
            >
                ➕ Add Todo
            </button>
        </div>
        @error('newTodo')
            <div class="text-red text-sm mt-2">{{ $message }}</div>
        @enderror
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
        <div class="mb-4">
            <input 
                wire:model="search" 
                wire:input="$refresh"
                type="text" 
                placeholder="Search todos..." 
                class="input"
            >
        </div>
        <div class="flex gap-2 flex-mobile">
            <button 
                wire:click="setFilter('all')"
                class="btn @if($filter === 'all') btn-primary @else btn-secondary @endif"
            >
                All ({{ $stats['total'] }})
            </button>
            <button 
                wire:click="setFilter('pending')"
                class="btn @if($filter === 'pending') btn-primary @else btn-secondary @endif"
            >
                Pending ({{ $stats['pending'] }})
            </button>
            <button 
                wire:click="setFilter('completed')"
                class="btn @if($filter === 'completed') btn-primary @else btn-secondary @endif"
            >
                Completed ({{ $stats['completed'] }})
            </button>
        </div>
    </div>

    <!-- Todos List -->
    <div class="space-y-3 mb-6">
        @forelse($todos as $todo)
            <div class="todo-item" wire:key="todo-{{ $todo->id }}">
                <input 
                    type="checkbox" 
                    {{ $todo->completed ? 'checked' : '' }}
                    wire:click="toggleTodo({{ $todo->id }})"
                    class="checkbox"
                >
                <div class="flex-1">
                    <span class="{{ $todo->completed ? 'line-through text-gray' : 'text-black font-medium' }}">
                        {{ $todo->title }}
                    </span>
                </div>
                <div class="flex gap-2">
                    <button 
                        onclick="editTodo({{ $todo->id }}, '{{ addslashes($todo->title) }}')"
                        class="icon-btn text-blue" 
                        title="Edit todo"
                    >
                        ✏️
                    </button>
                    <button 
                        wire:click="showDeleteModal({{ $todo->id }})"
                        class="icon-btn text-red" 
                        title="Delete todo"
                    >
                        🗑️
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="text-4xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-gray mb-2">No todos found</h3>
                <p class="text-gray">
                    @if($search)
                        Try adjusting your search terms
                    @else
                        Add your first todo to get started!
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($totalPages > 1)
        <div class="flex justify-center gap-2 mb-6">
            @if($currentPage > 1)
                <button wire:click="changePage({{ $currentPage - 1 }})" class="btn btn-secondary">‹ Previous</button>
            @endif
            
            @for($i = 1; $i <= $totalPages; $i++)
                <button 
                    wire:click="changePage({{ $i }})"
                    class="btn @if($currentPage === $i) btn-primary @else btn-secondary @endif"
                >
                    {{ $i }}
                </button>
            @endfor
            
            @if($currentPage < $totalPages)
                <button wire:click="changePage({{ $currentPage + 1 }})" class="btn btn-secondary">Next ›</button>
            @endif
        </div>
    @endif

    <!-- Stats -->
    <div class="mt-8 pt-6 border-t">
        <div class="grid grid-3">
            <div class="bg-blue-50 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-blue">{{ $stats['total'] }}</div>
                <div class="text-blue font-medium">Total Tasks</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-yellow">{{ $stats['pending'] }}</div>
                <div class="text-yellow font-medium">Pending</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-green">{{ $stats['completed'] }}</div>
                <div class="text-green font-medium">Completed</div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-overlay modal">
            <div class="modal-content">
                <div class="text-center">
                    <div class="text-4xl mb-4">🗑️</div>
                    <h3 class="text-lg font-semibold mb-2">Delete Todo</h3>
                    <p class="text-gray mb-6">Are you sure you want to delete this todo? This action cannot be undone.</p>
                    <div class="flex gap-3 justify-center">
                        <button wire:click="hideDeleteModal" class="btn btn-secondary">Cancel</button>
                        <button wire:click="confirmDelete" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function editTodo(id, currentTitle) {
        const newTitle = prompt('Edit todo:', currentTitle);
        if (newTitle !== null && newTitle.trim() !== '') {
            @this.call('updateTodo', id, newTitle.trim());
        }
    }
</script>