<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Sokol\Application\Services\UserService;

class VerifyEmailController extends Controller
{
    public function __construct(
        private readonly UserService $users,
    ) {}

    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $this->users->verifyEmail($this->currentUserId());

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
