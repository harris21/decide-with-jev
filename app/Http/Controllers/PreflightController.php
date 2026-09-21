<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PreflightController extends Controller
{
    public function show(): View
    {
        Gate::authorize('edit-preflight');

        return view('preflight', ['draft' => null]);
    }
}
