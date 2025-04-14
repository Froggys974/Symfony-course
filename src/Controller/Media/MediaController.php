<?php

namespace App\Controller\Media;

use App\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/media', name: 'app_media_')]
final class MediaController extends AbstractController{

    #[Route('/lists', name: 'lists')]
    public function mediaList(): Response
    {

        return $this->render('media/lists.html.twig', [
            // 'movie' => $idMovie,
        ]);
    }
}
