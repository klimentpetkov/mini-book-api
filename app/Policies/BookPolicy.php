<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('book.viewAny') || $user->can('book.view');
    }

    public function view(User $user, Book $book): bool
    {
        return $user->can('book.view');
    }

    public function create(User $user): bool
    {
        return $user->can('book.create');
    }

    public function update(User $user, Book $book): bool
    {
        return $user->can('book.update');
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->can('book.delete');
    }
}
