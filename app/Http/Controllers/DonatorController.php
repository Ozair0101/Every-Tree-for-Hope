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
        // Get featured donators for the main sections
        $featuredDonators = Donator::featured()->verified()->get();
        
        // Get all verified donators for the table
        $verifiedDonators = Donator::verified()->orderBy('trees_sponsored', 'desc')->get();
        
        // Get statistics
        $totalDonators = Donator::verified()->count();
        $totalTrees = Donator::verified()->sum('trees_sponsored');
        $totalFinancial = Donator::verified()->sum('financial_support');
        
        return view('pages.donators', compact(
            'featuredDonators',
            'verifiedDonators',
            'totalDonators',
            'totalTrees',
            'totalFinancial'
        ));
    }
}
