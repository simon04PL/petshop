<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PetController extends Controller
{
    public function index()
    {
        $response = Http::get('https://petstore.swagger.io/v2/pet/findByStatus', ['status' => 'available']);
        $pets = $response->json();
        return view('pets.index', compact('pets'));
    }

    public function store(Request $request)
    {
        Http::post('https://petstore.swagger.io/v2/pet', $request->all());
        return redirect()->route('pets.index')->with('status', 'Pet added successfully!');
    }

    public function update(Request $request, $id)
    {
        Http::put('https://petstore.swagger.io/v2/pet', $request->all());
        return redirect()->route('pets.index')->with('status', 'Pet updated successfully!');
    }

    public function destroy($id)
    {
        Http::delete("https://petstore.swagger.io/v2/pet/{$id}");
        return redirect()->route('pets.index')->with('status', 'Pet deleted successfully!');
    }
}

