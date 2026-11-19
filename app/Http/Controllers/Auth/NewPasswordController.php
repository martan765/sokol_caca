<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Support\PasswordResetMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Sokol\Application\Abstraction\UserServiceInterface;
use Sokol\Domain\Enums\PasswordResetResult;

class NewPasswordController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $users,
    ) {}

    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $result = $this->users->resetPassword(
            (string) $request->input('email'),
            (string) $request->input('token'),
            (string) $request->input('password'),
        );
        $status = PasswordResetMessages::key($result);

        return $result === PasswordResetResult::Reset
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
