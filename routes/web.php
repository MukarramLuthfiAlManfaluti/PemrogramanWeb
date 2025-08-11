<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Todos;

Route::get('/', Todos::class)->name('todos');