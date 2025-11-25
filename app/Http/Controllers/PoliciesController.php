<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoliciesController extends Controller
{
    public function show($policy = 'privacy')
    {
        // Validate policy type
        $validPolicies = ['privacy', 'terms', 'shipping', 'refund'];
        if (!in_array($policy, $validPolicies)) {
            abort(404);
        }

        return view('policies', ['activePolicy' => $policy]);
    }
}
