<?php

namespace App\Http\Controllers;
use App\Models\Agent;
use Illuminate\Http\Request;

class propertyagentController extends Controller
{
    public function PropertyAgent()
    {
        return view("property-agent");
    }

    public function showagent()
    {
        // Ambil semua data agen dari database
        $agents = Agent::all();
        // Kirim data agen ke view
        return view('property-agent', compact('agents'));
    }
    public function showagentindex()
    {
        // Ambil semua data agen dari database
        $agents = Agent::all();
        // Kirim data agen ke view
        return view('index', compact('agents'));
    }
}
