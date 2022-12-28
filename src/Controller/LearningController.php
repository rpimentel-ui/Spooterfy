<?php
// src/Controller/LearningController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\u;
use SpotifyWebAPI\SpotifyWebAPI;


class LearningController extends AbstractController 
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private $endpoint = "http://ws.audioscrobbler.com/2.0/";

    public function endpoint($endpoint) { 
        $this->endpoint = $endpoint; 
    }

    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {

        $response = $this->client->request('GET', $this->endpoint . '?method=chart.gettopartists&api_key=57ee3318536b23ee81d6b27e36997cde&format=json');

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $contents = $response->toArray();
        
        return $this->render('vinyl/homepage.html.twig', [
            'title' => "Today's Top Artists",
            'artists' => $contents['artists']['artist']
        ]);

    }

    #[Route('/browse', name: 'app_browse')]
    public function browse(SpotifyWebAPI $api): Response
    {
        $data = $api->getCategoriesList([
            'limit' => 10,
            'offset' => 0,
        ]);

        $title = !empty($genre) ? $genre : "All Genres";
        return $this->render('vinyl/browse.html.twig', [
            'title' => $title,
            'genres' => $data->categories->items
        ]);
    }

    


    #[Route('/browse/{genre}/{id}', name: 'app_browse_genre')]
    public function browseGenre(string $genre = NULL, string $id = NULL, SpotifyWebAPI $api): Response
    {

        $data = $api->getRecommendations([
            'seed_genres' => $genre,
            'limit' => 10,
            'offset' => 0,
        ]);


        dd($data);

        $search = $api->search($genre, 'recommendations');
        return new JsonResponse($search); 

        $title = !empty($genre) ? $genre : "All Genres";
        return $this->render('vinyl/browse.html.twig', [
            'title' => $title,
            'genres' => $data->playlists->items
        ]);
    }
}