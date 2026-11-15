<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Sokol\Application\Services\UserService;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly UserService $users,
    ) {}

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $user = $this->users->register($request->toData());

        Auth::loginUsingId($user->id);

        return redirect(route('dashboard', absolute: false));
    }
}
