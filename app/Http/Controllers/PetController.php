<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PetController extends Controller
{
    public function index()
    {
        $statuses = ['available', 'pending', 'sold'];
        $allPets = [];

        foreach ($statuses as $status) {
            //applies to all api endpoints. You can add them in env but in this case there is no need. This is not a secret api then let me use the api implementation method directly in the controller
            $response = Http::get("https://petstore.swagger.io/v2/pet/findByStatus", ['status' => $status]);
            if ($response->successful()) {
                $allPets = array_merge($allPets, $response->json());
            }

            if ($response->failed()) {
                return back()->withErrors(['api_error' => 'Failed to fetch pets from API. Please try again later.']);
            }
        }

        return view('pets.index', compact('allPets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'photoUrls' => 'required|array',
            //should be url and not string but as I verified the data is not which have string values and when editing the user could have strange errors that he would not understand. In this way I ensure the consistency of the application
            'photoUrls.*' => 'required|string',
            'status' => 'required|string|in:available,pending,sold',
            'category.name' => 'nullable|string',
            'tags.*.name' => 'nullable|string',
        ]);

        $id = $request->input('id');

        $response = Http::get("https://petstore.swagger.io/v2/pet/{$id}");
        if ($response->status() !== 404) {
            return redirect()->route('pets.index')->with('error', "Pet with ID {$id} already exists.");
        }

        $createResponse = Http::post("https://petstore.swagger.io/v2/pet", $request->all());
        if ($createResponse->successful()) {
            return redirect()->route('pets.index')->with('status', "Pet with ID {$id} added successfully!");
        }

        return redirect()->route('pets.index')->with('error', 'Failed to add the pet.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'photoUrls' => 'required|array',
            'photoUrls.*' => 'required|url',
            'status' => 'required|string|in:available,pending,sold',
            'category.name' => 'nullable|string',
            'tags.*.name' => 'nullable|string',
        ]);

        $response = Http::get("https://petstore.swagger.io/v2/pet/{$id}");
        if ($response->status() === 404) {
            return redirect()->route('pets.index')->with('error', "Pet with ID {$id} does not exist.");
        }

        $updateResponse = Http::put("https://petstore.swagger.io/v2/pet", array_merge($request->all(), ['id' => $id]));
        if ($updateResponse->successful()) {
            return redirect()->route('pets.index')->with('status', "Pet with ID {$id} updated successfully!");
        }

        return redirect()->route('pets.index')->with('error', 'Failed to update the pet.');
    }

    public function delete($id)
    {
        $response = Http::get("https://petstore.swagger.io/v2/pet/{$id}");
        if ($response->status() === 404) {
            return redirect()->route('pets.index')->with('error', "Pet with ID {$id} does not exist.");
        }

        $deleteResponse = Http::delete("https://petstore.swagger.io/v2/pet/{$id}");
        if ($deleteResponse->successful()) {
            return redirect()->route('pets.index')->with('status', "Pet with ID {$id} deleted successfully!");
        }

        return redirect()->route('pets.index')->with('error', 'Failed to delete the pet.');
    }
}