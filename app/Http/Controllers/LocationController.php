<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class LocationController extends Controller
{

    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('azure.map_key');
    }

     public function searchAddress(Request $request)
    {
        $query = $request->query('query');
        $endpoint = config('azure.map_autocomplete_endpoint');

        if (!$query) return response()->json(['results' => []]);

        $response = Http::get($endpoint, [
            'subscription-key' => $this->apiKey,
            'query' => $query,
            'language' => 'id-ID',
            'limit' => 5,
            'countrySet' => 'ID'
        ]);

        return $response->json();
    }

    public function reverseGeocode(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $endpoint = config('azure.map_reverse_geolocation_endpoint');

        if (!$lat || !$lng) return response()->json(['error' => 'Invalid coordinates'], 400);

        $response = Http::get($endpoint, [
            'subscription-key' => $this->apiKey,
            'query' => "{$lat},{$lng}",
            'language' => 'id-ID'
        ]);

        return $response->json();
    }
}
