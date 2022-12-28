<?php
// src/Controller/SongAPIController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\u;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use SpotifyWebAPI\SpotifyWebAPI;


class SongAPIController extends AbstractController 
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/api/songs/{id<\d+>}', methods: ['GET'])] //Using a Regex for numeric only
    public function getSong(int $id, LoggerInterface $logger): Response 
    {
        $genres = $this->getGenres();
        dd($genres);
        // TODO query the database
        $song = [
            'id' => $id,
            'name' => 'Waterfalls',
            'url' => 'https://symfonycasts.s3.amazonaws.com/sample.mp3',
        ];

        $logger->info("Returning API response for song {song}", [
            'song' => $song
        ]);

        return new JsonResponse($song);
        //return $this->json($song);
    }

    #[Route('/spotify', name: 'app_spotify')]
    public function testing(SpotifyWebAPI $api)
    {
        $search = $api->search('Thriller', 'album');
        return new JsonResponse($search);
    }

}