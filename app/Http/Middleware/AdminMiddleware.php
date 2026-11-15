<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Sokol\Application\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function __construct(
        private readonly UserService $users,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $id   = Auth::id();
        $user = $this->users->current($id === null ? null : (int) $id);

        if ($user?->isAdmin) {
            // Každý záznam v logu během tohoto požadavku ponese, kdo akci provedl
            Context::add('admin', $user->email);

            return $next($request);
        }

        Log::warning('Pokus o přístup do administrace bez oprávnění', [
            'user' => $user?->email,
            'url'  => $request->fullUrl(),
        ]);

        if ($request->expectsJson()) {
            abort(403, 'Sem smí jen trenér!');
        }

        return redirect()->route('dashboard')->with('error', 'Sem smí jen trenér!');
    }
}
