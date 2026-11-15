<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Support\PasswordResetMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Sokol\Application\Dto\PasswordResetResult;
use Sokol\Application\Services\UserService;

class PasswordResetLinkController extends Controller
{
    public function __construct(
        private readonly UserService $users,
    ) {}

    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $result = $this->users->sendPasswordResetLink((string) $request->input('email'));
        $status = PasswordResetMessages::key($result);

        return $result === PasswordResetResult::LinkSent
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
