<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Inertia\Inertia;
use Inertia\Response;

class ModuleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('SuperAdmin/Modules/Index', [
            'modules' => Module::query()->orderBy('category')->orderBy('name')->get(),
        ]);
    }
}
