<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Support\Facades\Session;


use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function AgentIn()
{
    return view('agent.indexAgent', compact('properties', 'hotListings'));
}
public function myProperties()
{
    $id_agent = Session::get('id_account') ?? $_COOKIE['id_account'] ?? null;

    if (!$id_agent) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    $properties = Property::where('id_agent', $id_agent)->paginate(6);

    return view('my-properties', compact('properties'));
}
}
