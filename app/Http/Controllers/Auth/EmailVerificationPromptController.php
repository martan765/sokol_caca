<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Sokol\Application\Abstraction\UserServiceInterface;

class EmailVerificationPromptController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $users,
    ) {}

    /**
     * Display the email verification prompt.
     */
    public function __invoke(): RedirectResponse|View
    {
        return $this->users->hasVerifiedEmail($this->currentUserId())
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
