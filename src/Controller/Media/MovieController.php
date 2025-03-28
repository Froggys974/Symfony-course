<?php

namespace App\Controller\Media;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class MovieController extends AbstractController{
    #[Route('/media/movie', name: 'app_media_movie')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Media/MovieController.php',
        ]);
    }
}
