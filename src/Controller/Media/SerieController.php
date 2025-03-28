<?php

namespace App\Controller\Media;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SerieController extends AbstractController{
    #[Route('/media/serie', name: 'app_media_serie')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Media/serieController.php',
        ]);
    }
}
