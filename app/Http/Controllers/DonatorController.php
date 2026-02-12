<?php

namespace App\Http\Controllers;

use App\Models\Donator;
use Illuminate\Http\Request;

class DonatorController extends Controller
{
    /**
     * Display the donators page.
     */
    public function index()
    {
        // Get the last 5 featured donators for the main sections
        $featuredDonators = Donator::featured()->verified()->latest()->take(5)->get();
        
        // Get the featured donator with the highest financial support for Individual Impact section
        $topSupporter = Donator::featured()->verified()->orderBy('financial_support', 'desc')->first();
        
        // Get all verified donators for the table (latest first)
        $verifiedDonators = Donator::verified()->orderBy('created_at', 'desc')->get();
        
        // Get statistics
        $totalDonators = Donator::verified()->count();
        $totalTrees = Donator::verified()->sum('trees_sponsored');
        $totalFinancial = Donator::verified()->sum('financial_support');
        
        return view('pages.donators', compact(
            'featuredDonators',
            'topSupporter',
            'verifiedDonators',
            'totalDonators',
            'totalTrees',
            'totalFinancial'
        ));
    }
}
