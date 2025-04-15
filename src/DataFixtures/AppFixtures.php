<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\Movie;
use App\Entity\Playlist;
use App\Entity\PlaylistMedia;
use App\Entity\PlaylistSubscription;
use App\Entity\Season;
use App\Entity\Serie;
use App\Entity\Subscription;
use App\Entity\SubscriptionHistory;
use App\Entity\User;
use App\Entity\WatchHistory;
use App\Enum\CommentStatus;
use App\Enum\UserAccountStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class AppFixtures extends Fixture
{
    private const MAX_USERS = 30;
    private const MAX_MEDIA = 100;
    private const MAX_NB_PLAYLIST = 4;
    private const MAX_MEDIA_PLAYLIST = 10;
    private $faker;
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {

        $categories = [];
        $categoryNames = ['Action', 'Aventure', 'Comedie', 'Drame', 'Fantastique', 'Horreur', 'Romance'];
        foreach ($categoryNames as $name) {
            $category = $this->createCategory($name, $manager);
            $categories[] = $category;
        }

        $languages = [];
        $languageNames = ['Francais', 'Anglais', 'Espagnol', 'Allemand', 'Italien', 'Portugais'];
        foreach ($languageNames as $name) {
            $language = $this->createLanguage($name, $manager);
            $languages[] = $language;
        }

        $users = [];
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@admin.fr');
        $admin->setPlainPassword('admin');
        //roles ADMIN
        $admin->setAccountStatus(UserAccountStatusEnum::ACTIVE);
        $manager->persist($admin);
        $users[] = $admin;
        for ($i = 0; $i < self::MAX_USERS; $i++) {
            $user = new User();
            $user = $this->createUser($manager);
            $users[] = $user;
        }
        $medias = [];
        for ($i = 0; $i < self::MAX_MEDIA; $i++) {
            $media = $this->createMedia($manager);
            $medias[] = $media;
        }

        $playlists = [];
        foreach ($users as $user) {
            $randomNbPlaylist = random_int(0, self::MAX_NB_PLAYLIST);
            for ($i = 0; $i < $randomNbPlaylist; $i++) {
                $playlist = $this->createPlaylist($manager, $user);
                $playlists[] = $playlist;
                foreach ($this->faker->randomElements($medias, rand(1, self::MAX_MEDIA_PLAYLIST)) as $media) {
                    $playlistMedia = $this->createPlaylistMedia($media, $playlist);
                    $manager->persist($playlistMedia);
    
                }
            }

            
            $playlistSubscription = $this->creatPlaylistSubscription($user, $playlist);
            $manager->persist($playlistSubscription);
            $playlists[] = $playlist;
        }

        $subscriptions = [];
        $arraySubscription = [
            ['name' => 'HD', 'duration' => 1],
            ['name' => 'AUltra-HD', 'duration' => 3],
            ['name' => '4K', 'duration' => 6],
            ['name' => '4K-Family', 'duration' => 12],
        ];
        foreach ($arraySubscription as $value) {
            $subscription = $this->createSubscription($value);
            $manager->persist($subscription);
            $subscriptions[] = $subscription;
        }

        foreach ($users as $user) {
            if ($user->getUsername() !== 'admin') {
                $user->setCurrentSubscription($this->faker->randomElement($subscriptions));
                $manager->persist($user);
            }
            for ($j = 0; $j < rand(0, 6); $j++) {
                $subscriptionHistory = $this->createSubscriptionHistory($subscriptions, $user);
                $manager->persist($subscriptionHistory);
            }

            $randomMedias = $this->faker->randomElements($medias, rand(0, 5));
            foreach ($randomMedias as $media) {
                $watchHistory = $this->createWatchHistory($user, $media);
                $manager->persist($watchHistory);
            }
        }

        foreach ($medias as $media) {
            $media->addCategory($this->faker->randomElement($categories));
            $media->addLanguage($this->faker->randomElement($languages));
            $manager->persist($media);
            $comment=$this->createMediaComment($manager,$users,$medias);
            $manager->persist($comment);

        }





        $manager->flush();
    }

    private function createCategory($name, $manager)
    {
        $category = new Category();
        $category->setName(strtolower($name));
        $category->setLabel($name);
        $manager->persist($category);
        return $category;
    }

    private function createLanguage($name, $manager)
    {
        $language = new Language();
        $language->setName($name);
        $language->setCode(substr(strtolower($name), 0, 2));
        $manager->persist($language);
        return $language;
    }

    private function createUser($manager)
    {
        $user = new User();
        $user->setUsername($this->faker->userName);
        $user->setEmail($this->faker->email);
        $user->setPlainPassword($this->faker->password);
        $user->setAccountStatus(UserAccountStatusEnum::ACTIVE);
        $manager->persist($user);
        return $user;
    }

    private function createMedia($manager): Media
    {
        $media = random_int(1, 2) == 1 ? new Movie() : new Serie();
        $media->setTitle($this->faker->sentence(3));
        $media->setShortDescription($this->faker->text(200));
        $media->setLongDescription($this->faker->text(1000));
        $media->setReleaseDate($this->faker->dateTimeThisCentury());
        $media->setCoverImage($this->faker->imageUrl());
        $media->setStaff([$this->faker->name, $this->faker->name, $this->faker->name]);
        $media->setCastList([$this->faker->name, $this->faker->name, $this->faker->name]);
        if ($media instanceof Serie) {
            $media = $this->createSeasonAndEpisodes($media, $manager);
        }
        $manager->persist($media);
        return $media;
    }

    private function createSeasonAndEpisodes($serie, $manager)
    {
        for ($i = 0; $i < random_int(1, 10); $i++) {
            $season = new Season();
            $season->setSeasonNumber($i + 1);
            for ($j = 0; $j < random_int(1, 15); $j++) {
                $episode = new Episode();
                $episode->setTitle($this->faker->sentence(3));
                $episode->setDuration($this->faker->dateTimeBetween('00:25:00', '00:50:00'));
                $episode->setReleaseDate($this->faker->dateTimeThisYear());
                $episode->setSeason($season);
                $manager->persist($episode);
            }
            $season->setSerie($serie);
            $manager->persist($season);
        }
        return $serie;
    }

    private function createPlaylist($manager, $user): Playlist
    {
        $playlist = new Playlist();
        $playlist->setName($this->faker->name());
        $playlist->setUserCreator($user);
        $playlist->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear()));
        $playlist->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear()));
        $manager->persist($playlist);
        return $playlist;
    }

    private function createPlaylistMedia($media, $playlist): PlaylistMedia
    {
        $playlistMedia = new PlaylistMedia();
        $playlistMedia->setMedia($media);
        $playlistMedia->setPlaylist($playlist);
        $playlistMedia->setAddedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear()));
        return $playlistMedia;
    }

    private function creatPlaylistSubscription($user, $playlist): PlaylistSubscription
    {
        $playlistSubscription = new PlaylistSubscription();
        $playlistSubscription->setSubscribedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear()));
        $playlistSubscription->setUserSub($user);
        $playlistSubscription->setPlaylist($playlist);
        return $playlistSubscription;
    }

    private function createSubscription($value)
    {
        $subscription = new Subscription();
        $subscription->setName($value['name']);
        $subscription->setDurationInMonths($value['duration']);
        $subscription->setPrice($this->faker->randomFloat(2, 5, 50));
        return $subscription;
    }

    private function createSubscriptionHistory($subscriptions, $user)
    {
        $subscriptionHistory = new SubscriptionHistory();
        $subscriptionHistory->setSubscription($this->faker->randomElement($subscriptions));
        $subscriptionHistory->setUserSub($user);
        $subscriptionHistory->setStartDate(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear()));
        $subscriptionHistory->setEndDate(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear()));
        return $subscriptionHistory;
    }

    private function createWatchHistory($user, $media)
    {
        $watchHistory = new WatchHistory();
        $watchHistory->setUserWatch($user);
        $watchHistory->setMedia($media);
        $watchHistory->setNumberOfViews($this->faker->numberBetween(1,50));
        $watchHistory->setLastWatched($this->faker->dateTimeThisYear());
        return $watchHistory;

    }

    private function createMediaComment($manager,$users, $medias)
    {
        $comment = new Comment();
        $comment->setUserComment($this->faker->randomElement($users));
        $comment->setMedia($this->faker->randomElement($medias));
        $comment->setContent($this->faker->text(200));
        $comment->setStatusComment(CommentStatus::PUBLISH);
        if ($this->faker->boolean(40)) {
            for ($i=0; $i < rand(1,5) ; $i++) { 
                $reply = new Comment();
                $reply->setUserComment($this->faker->randomElement($users));
                $reply->setMedia($comment->getMedia());
                $reply->setContent($this->faker->text(200));
                $reply->setStatusComment(CommentStatus::PUBLISH);
                $manager->persist($reply);
                $comment->addCommentResponse($reply);
            }
        }
        return $comment;
    }
}
