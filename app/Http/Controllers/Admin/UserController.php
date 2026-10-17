<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;
use Psr\Log\LoggerInterface;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $users,
        private readonly LoggerInterface $logger,
    ) {}

    public function index()
    {
        $users = $this->users->paginated();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $this->users->updateByAdmin($user, $request->validated());

        $this->logger->info('Uživatel upraven', ['user_id' => $user->id, 'changes' => $user->getChanges()]);

        return redirect()->route('admin.users.index')->with('success', 'Uživatel byl upraven.');
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $this->users->delete($user);

        $this->logger->warning('Uživatel smazán', ['user_id' => $user->id, 'email' => $user->email]);

        return redirect()->route('admin.users.index')->with('success', 'Uživatel byl smazán.');
    }
}
