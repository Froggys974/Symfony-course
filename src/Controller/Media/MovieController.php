<?php

namespace App\Controller\Media;

use App\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/movie', name: 'app_movie_')]
final class MovieController extends AbstractController{

    #[Route('/detail/{idMovie}', name: 'detail')]
    public function MovieDetail(Movie $idMovie): Response
    {
        // TODO: watch history by user
        return $this->render('media/detail.html.twig', [
            'movie' => $idMovie,
        ]);
    }
}
