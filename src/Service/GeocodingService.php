<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCoordinates(string $address): ?array
    {
        $url = 'https://nominatim.openstreetmap.org/search';
        $params = [
            'q' => $address,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 1,
        ];

        $response = $this->client->request('GET', $url, ['query' => $params]);
        $data = $response->toArray();

        if (!empty($data)) {
            return [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon'],
            ];
        }

        return null;
    }
}
