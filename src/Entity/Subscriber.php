<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SubscriberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Controller\NewsletterController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
#[ApiResource]
#[ApiResource (
    normalizationContext: ['groups' => ['read']],
    operations: [
    new Post(),
    new Put(),
    new Delete(),
    new Patch(),
    new Post(
        uriTemplate: '/api/newsletter/subscribe',
        controller: NewsletterController::class,
        name: 'api_newsletter_subscribe'
    ),
    new Post(
        uriTemplate: '/api/newsletter/unsubscribe',
        controller: NewsletterController::class,
        name: 'api_newsletter_unsubscribe'
    )
]
)]
class Subscriber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read:collection'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    private ?bool $isSubscribed = null;

    public function __construct()
   {
    $this->isSubscribed = true; // Valeur par défaut
   }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isSubscribed(): ?bool
    {
        return $this->isSubscribed;
    }

    public function setSubscribed(bool $isSubscribed): static
    {
        $this->isSubscribed = $isSubscribed;

        return $this;
    }
}
