<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PetstoreService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.petstore.base_url');
    }

    public function getPets()
    {
        $statuses = ['available', 'pending', 'sold'];
        $allPets = [];
    
        foreach ($statuses as $status) {
            $response = Http::get("{$this->baseUrl}/v2/pet/findByStatus", [
                'status' => $status,
            ]);
    
            if ($response->failed()) {
                throw new \Exception("Failed to fetch pets with status: $status");
            }
    
            $allPets = array_merge($allPets, $response->json());
        }
    
        return $allPets;
    }
    
    public function petExists($id)
    {
        $response = Http::get("{$this->baseUrl}/v2/pet/{$id}");
    
        if ($response->status() === 404) {
            return false;
        }
    
        if ($response->successful()) {
            return true;
        }
    
        throw new \Exception('Failed to check if pet exists.');
    }
    

    public function addPet(array $data)
    {
        return Http::post("{$this->baseUrl}/v2/pet", $data)->json();
    }

    public function updatePet(array $data)
    {
        return Http::put("{$this->baseUrl}/v2/pet", $data)->json();
    }

    public function deletePet($petId)
    {
        return Http::delete("{$this->baseUrl}/v2/pet/{$petId}")->json();
    }
}
