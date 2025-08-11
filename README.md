# Todo App - Laravel Livewire Style

A modern, responsive Todo application built with PHP-like structure mimicking Laravel Livewire functionality using Alpine.js and Tailwind CSS.

## Features

### ✅ Complete CRUD Operations
- **Create**: Add new todos with validation
- **Read**: View all todos with real-time filtering
- **Update**: Edit todos inline with save/cancel options
- **Delete**: Remove todos with confirmation modal

### 🎯 Advanced Functionality
- **Search**: Filter todos by title
- **Status Filter**: View all, pending, or completed todos
- **Pagination**: Display todos in pages (5 per page)
- **Local Storage**: Persist data between sessions
- **Validation**: Input validation with error messages
- **Flash Messages**: Success notifications for actions

### 🎨 User Interface
- **Responsive Design**: Works on desktop and mobile
- **Modern UI**: Clean design with Tailwind CSS
- **Interactive Elements**: Smooth transitions and hover effects
- **Icons**: FontAwesome icons for better UX
- **Modal Dialogs**: Confirmation modal for deletions

### 📊 Statistics Dashboard
- Total tasks counter
- Pending tasks counter
- Completed tasks counter

## Laravel Livewire Inspired Features

This implementation mimics Laravel Livewire patterns:

1. **Component State Management**: Using Alpine.js reactive data
2. **Event Handling**: Method calls for user interactions
3. **Real-time Updates**: Automatic UI updates on data changes
4. **Validation**: Client-side validation with error display
5. **Flash Messages**: Success/error message system
6. **Pagination**: Built-in pagination functionality

## Technical Implementation

### Frontend Technologies
- **Alpine.js**: For reactive JavaScript functionality
- **Tailwind CSS**: For responsive styling
- **FontAwesome**: For icons
- **Local Storage**: For data persistence

### File Structure
```
├── index.html          # Main application file
├── README.md          # Documentation
└── assets/            # (Future: CSS/JS files)
```

### Key Components

#### Todo Management
- Add new todos with Enter key or button click
- Toggle completion status with checkboxes
- Inline editing with OK/Cancel buttons
- Delete with confirmation modal

#### Search and Filter
- Real-time search by todo title
- Filter by status (All/Pending/Completed)
- Counters showing items in each category

#### Pagination
- Configurable items per page (default: 5)
- Navigation buttons (Previous/Next)
- Page number buttons
- Automatic page adjustment on filtering

## Data Structure

Each todo item contains:
```javascript
{
    id: timestamp,           // Unique identifier
    title: "Todo title",     // Todo text
    completed: false,        // Completion status
    editing: false,          // Edit mode flag
    editTitle: "Edit text"   // Temporary edit text
}
```

## Laravel Migration Equivalent

If this were a Laravel application, the database migration would be:

```php
Schema::create('todos', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->boolean('completed')->default(false);
    $table->timestamps();
});
```

## Laravel Model Equivalent

```php
class Todo extends Model
{
    protected $fillable = ['title', 'completed'];
    protected $casts = ['completed' => 'boolean'];
}
```

## Validation Rules

- Title is required
- Title must not be empty after trimming
- Title must be less than 255 characters

## Browser Compatibility

- Modern browsers with ES6+ support
- Works with JavaScript enabled
- Responsive design for mobile devices

## Getting Started

1. Open `index.html` in a web browser
2. Start adding todos
3. Use search and filters to manage tasks
4. Data persists between browser sessions

## Future Enhancements

- Backend PHP API for server-side storage
- User authentication
- Todo categories/tags
- Due dates and reminders
- Drag and drop reordering
- Export/import functionality
- Dark mode theme

## License

This project is open source and available under the MIT License.