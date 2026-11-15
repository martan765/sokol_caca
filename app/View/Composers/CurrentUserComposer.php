<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Sokol\Application\Abstraction\UserServiceInterface;

class CurrentUserComposer
{
    public function __construct(
        private readonly UserServiceInterface $users,
    ) {}

    public function compose(View $view): void
    {
        $id = Auth::id();

        $view->with('currentUser', $this->users->current($id === null ? null : (int) $id));
    }
}
