<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Support\Paging;
use Illuminate\Support\Facades\Gate;
use Psr\Log\LoggerInterface;
use Sokol\Application\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $users,
        private readonly LoggerInterface $logger,
    ) {}

    public function index()
    {
        $users = Paging::toPaginator($this->users->paginated(Paging::currentPage()));

        return view('admin.users.index', compact('users'));
    }

    public function edit(int $user)
    {
        return view('admin.users.edit', ['user' => $this->users->get($user)]);
    }

    public function update(UserRequest $request, int $user)
    {
        $changes = $this->users->updateByAdmin($user, $request->toData());

        $this->logger->info('Uživatel upraven', ['user_id' => $user, 'changes' => $changes]);

        return redirect()->route('admin.users.index')->with('success', 'Uživatel byl upraven.');
    }

    public function destroy(int $user)
    {
        $this->users->get($user);
        Gate::authorize('delete-user', $user);

        $deleted = $this->users->delete($user);

        $this->logger->warning('Uživatel smazán', ['user_id' => $deleted->id, 'email' => $deleted->email]);

        return redirect()->route('admin.users.index')->with('success', 'Uživatel byl smazán.');
    }
}
