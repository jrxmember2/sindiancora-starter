<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class VersionController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('SuperAdmin/Versions/Index', [
            'currentVersion' => config('versioning.current'),
            'history' => array_values(config('versioning.history', [])),
        ]);
    }
}
