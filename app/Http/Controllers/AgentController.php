<?php

namespace App\Http\Controllers;
use App\Models\Property;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;



use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function AgentIn()
    {
        return view('agent.indexAgent', compact('properties', 'hotListings'));
    }

    public function myProperties()
{
    $id_account = Session::get('id_account') ?? $_COOKIE['id_account'] ?? null;

    if (!$id_account) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    // Ambil id_agent berdasarkan id_account
    $agent = DB::table('agent')
        ->where('id_account', $id_account)
        ->select('id_agent')
        ->first();

    if (!$agent) {
        return redirect()->route('dashboard')->with('error', 'Data agen tidak ditemukan.');
    }

    $properties = Property::where('id_agent', $agent->id_agent)->paginate(6);

    return view('my-properties', compact('properties'));
}

}
