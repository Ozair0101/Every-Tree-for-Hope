<?php

namespace App\Http\Controllers;

use App\Models\Donator;

class DonatorController extends Controller
{
    /**
     * Display the donators page — a clean wall of supporters
     * (full name, impact and profile image only).
     */
    public function index()
    {
        // All verified supporters, newest first, paginated for the wall
        $verifiedDonators = Donator::verified()
            ->orderBy('created_at', 'desc')
            ->paginate(18);

        $totalDonators = Donator::verified()->count();

        return view('pages.donators', compact(
            'verifiedDonators',
            'totalDonators',
        ));
    }
}
