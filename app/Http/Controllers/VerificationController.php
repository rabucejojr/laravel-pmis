<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->hasRole(['admin', 'verifier'])) {
            abort(403);
        }

        return view('verification.index');
    }
}
