<?php

namespace App\Controller\Media;

use App\Entity\Movie;
use App\Repository\PlaylistRepository;
use App\Repository\PlaylistSubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/media', name: 'app_media_')]
final class MediaController extends AbstractController
{
    private const MEDIA_PER_PAGE = 15;

    #[Route('/lists', name: 'lists')]
    public function mediaList(
        PlaylistRepository $playlistRepository,
        PlaylistSubscriptionRepository $playlistSubscriptionRepository,
        Request $request
    ): Response {
        $myPlaylists = $playlistRepository->findAll();
        $subscribedPlaylists = $playlistSubscriptionRepository->findAll();
        $playlistId = $request->query->get('playlist');

        if ($playlistId) {
            $playlist = $playlistRepository->find($playlistId);
        } else {
            $playlist =  !empty($myPlaylists) ? $myPlaylists[0] : (!empty($subscribedPlaylists) ? $subscribedPlaylists[0] : null);
        }
        //TODO: régler soucis playlist dupliqué

        $medias= $playlist->getAllPlaylistMediaByType(Movie::class);
        $movieCount = $medias->count();
        return $this->render('media/lists.html.twig', [
            'myPlaylists' => $myPlaylists,
            'subscribedPlaylists' => $subscribedPlaylists,
            'activePlaylist' => $playlist,
            'showMoreButton' => $movieCount > self::MEDIA_PER_PAGE,
            'playlistMedias' => $medias->slice(0, self::MEDIA_PER_PAGE),
            'mediaPerPage' => self::MEDIA_PER_PAGE
        ]);
    }
}
