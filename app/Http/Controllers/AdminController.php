<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function __invoke(Request $request)
    {
        Gate::authorize('access-admin');

        return redirect('/admin/videos');
    }
}
