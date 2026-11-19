<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function currentUserId(): int
    {
        return (int) Auth::id();
    }
}
