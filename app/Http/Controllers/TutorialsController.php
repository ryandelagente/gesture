<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class TutorialsController extends Controller
{
    public function index(?string $section = null)
    {
        $user = Auth::user();
        abort_unless($user, 403);
        return view('tutorials.index', [
            'user'    => $user,
            'section' => $section,
        ]);
    }
}
