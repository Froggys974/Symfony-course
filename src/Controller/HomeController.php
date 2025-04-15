<?php

namespace App\Controller;

use App\Entity\WatchHistory;
use App\Repository\MediaRepository;
use App\Repository\WatchHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    private const MAX_TENDANCE_MEDIA = 9;
    
    #[Route(path: '/home', name: "app_home")]
    public function index(
        WatchHistoryRepository $watchHistoryRepository
    ): Response {
        $tendance = $watchHistoryRepository->getTendanceMedia(self::MAX_TENDANCE_MEDIA);
        //TODO :implemenrer dans twig
        return $this->render('index.html.twig');
    }
}
