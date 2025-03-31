<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Controller\TicketController;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Annotation\Groups;



#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ApiResource ( operations: [
        new Get(),
        new Post(),
        new Put(),
        new Delete(),
        new Patch(),
        new Get(
            uriTemplate: 'api/ticket/validation',
            controller: TicketController::class,
            name: 'app_ticket_validation'
        ),
        new Get(
            uriTemplate: ' /api/ticket/search',
            controller: TicketController::class,
            name: 'search_ticket'),
        new GetCollection()
        
        ],
        normalizationContext: ['groups' => ['read:collection']]
    
)]
class Ticket
{
    #[ORM\Id]
    #[Groups(['read:collection'])]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]
    private ?string $code = null;
    #[Groups(['read:collection'])]
    #[ORM\Column]
    
    private ?bool $isClaimed = null;
    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:collection'])]
    private ?Lot $lot = null;

    #[ORM\ManyToOne(inversedBy: 'tikets')]
    #[Groups(['read:collection'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function isClaimed(): ?bool
    {
        return $this->isClaimed;
    }

    public function setClaimed(bool $isClaimed): static
    {
        $this->isClaimed = $isClaimed;

        return $this;
    }

    public function getLot(): ?Lot
    {
        return $this->lot;
    }

    public function setLot(?Lot $lot): static
    {
        $this->lot = $lot;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
