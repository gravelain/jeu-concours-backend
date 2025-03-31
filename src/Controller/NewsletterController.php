<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
namespace App\Controller;
use App\Entity\Subscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class NewsletterController extends AbstractController
{
    #[Route('/api/newsletter/subscribe', name: 'api_newsletter_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true); 

        if (!isset($data['email'])) {
            return new JsonResponse(['error' => 'Email is required'], 400);
        }

        // Check if the user already exists
        $existingSubscriber = $entityManager->getRepository(Subscriber::class)->findOneBy(['email' => $data['email']]);
        if ($existingSubscriber) {
            return new JsonResponse(['message' => 'Email already subscribed'], 400);
        }

        // Create a new subscriber
        $subscriber = new Subscriber();
        $subscriber->setEmail($data['email']);
        $subscriber->setCreatedAt(new \DateTime());

        $entityManager->persist($subscriber);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Subscription successful'], 201);
    }

    #[Route('/api/newsletter/unsubscribe', name: 'api_newsletter_unsubscribe', methods: ['POST'])]
    public function unsubscribe(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return new JsonResponse(['error' => 'Email is required'], 400);
        }

        // Find the subscriber
        $subscriber = $entityManager->getRepository(Subscriber::class)->findOneBy(['email' => $data['email']]);

        if (!$subscriber) {
            return new JsonResponse(['error' => 'Email not found'], 404);
        }

        // Unsubscribe
        $subscriber->setSubscribed(false);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Unsubscription successful'], 200);
    }   
}
