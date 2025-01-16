<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PetstoreService;

class PetController extends Controller
{
    protected $petstoreService;

    public function __construct(PetstoreService $petstoreService)
    {
        $this->petstoreService = $petstoreService;
    }

    public function index()
    {
        try {
            $allPets = $this->petstoreService->getPets();

            return view('pets.index', compact('allPets'));
        } catch (\Exception $e) {
            return back()->withErrors(['api_error' => 'Failed to fetch pets from API. Please try again later.']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'photoUrls' => 'required|array',
            'photoUrls.*' => 'required|string',
            'status' => 'required|string|in:available,pending,sold',
            'category.name' => 'nullable|string',
            'tags.*.name' => 'nullable|string',
        ]);

        try {
            $id = $request->input('id');

            if ($this->petstoreService->petExists($id)) {
                return redirect()->route('pets.index')->with('error', "Pet with ID {$id} already exists.");
            }

            $this->petstoreService->addPet($request->all());
            return redirect()->route('pets.index')->with('status', "Pet with ID {$id} added successfully!");
        } catch (\Exception $e) {
            return redirect()->route('pets.index')->with('error', 'Failed to add the pet.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'photoUrls' => 'required|array',
            'photoUrls.*' => 'required|string',
            'status' => 'required|string|in:available,pending,sold',
            'category.name' => 'nullable|string',
            'tags.*.name' => 'nullable|string',
        ]);

        try {
            if (!$this->petstoreService->petExists($id)) {
                return redirect()->route('pets.index')->with('error', "Pet with ID {$id} does not exist.");
            }

            $this->petstoreService->updatePet(array_merge($request->all(), ['id' => $id]));
            return redirect()->route('pets.index')->with('status', "Pet with ID {$id} updated successfully!");
        } catch (\Exception $e) {
            return redirect()->route('pets.index')->with('error', 'Failed to update the pet.');
        }
    }

    public function delete($id)
    {
        try {
            if (!$this->petstoreService->petExists($id)) {
                return redirect()->route('pets.index')->with('error', "Pet with ID {$id} does not exist.");
            }

            $this->petstoreService->deletePet($id);
            return redirect()->route('pets.index')->with('status', "Pet with ID {$id} deleted successfully!");
        } catch (\Exception $e) {
            return redirect()->route('pets.index')->with('error', 'Failed to delete the pet.');
        }
    }
}
