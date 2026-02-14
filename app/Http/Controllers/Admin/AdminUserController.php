<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AdminUserController extends Controller
{
    public function index()
    {
        Gate::authorize('access-admin');

        $users = User::query()->latest()->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }
}
