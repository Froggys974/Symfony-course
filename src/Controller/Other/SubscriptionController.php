<?php

namespace App\Controller\Other;

use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class SubscriptionController extends AbstractController{
    public function __construct(private SubscriptionRepository $subscriptionRepository)
    {
    }

    #[Route('/subscription', name: 'app_subscription')]
    public function index(): Response
    {
        return $this->render('other_content/subscriptions.html.twig', [
            'title' => 'Abonnements',
            'subscriptions' => $this->subscriptionRepository->findAll(),
            'userSubscriptions' => $this->getUser()->getCurrentSubscription(),
        ]);
    }
}
