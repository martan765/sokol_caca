<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Sokol\Application\Services\UserService;

class EmailVerificationNotificationController extends Controller
{
    public function __construct(
        private readonly UserService $users,
    ) {}

    /**
     * Send a new email verification notification.
     */
    public function store(): RedirectResponse
    {
        $userId = $this->currentUserId();

        if ($this->users->hasVerifiedEmail($userId)) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $this->users->sendEmailVerification($userId);

        return back()->with('status', 'verification-link-sent');
    }
}
