<?php

namespace App\Entity;

use App\Repository\LotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Delete;
use App\Controller\TicketController;


#[ORM\Entity(repositoryClass: LotRepository::class)]
#[ApiResource ( operations: [
        new Get(),
        new Get(
            uriTemplate: 'api/user/lot',
            controller: TicketController::class,
            name: 'user_prizes'
        ),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
        new Patch(),
       
        
        ],
        normalizationContext: ['groups' => ['read:collection']]
    
)]


class Lot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection'])]
    private ?int $id = null;
   

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]
    private ?string $name = null;


    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['read:collection'])]
    private ?string $value = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'lot')]
    private Collection $tickets;

    #[ORM\Column(nullable: true)]
    private ?int $Pourcentage = null;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setLot($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getLot() === $this) {
                $ticket->setLot(null);
            }
        }

        return $this;
    }

    public function getPourcentage(): ?int
    {
        return $this->Pourcentage;
    }

    public function setPourcentage(?int $Pourcentage): static
    {
        $this->Pourcentage = $Pourcentage;

        return $this;
    }
}

