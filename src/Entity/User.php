<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\TicketController;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(operations: [
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
    new GetCollection()
    
    ],normalizationContext:[ 'groups'=>['read:collection']])]

class User implements UserInterface, PasswordAuthenticatedUserInterface /*, /*TwoFactorInterface*/
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]

    private ?string $firstName = null;
   
    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]
    private ?string $Email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:collection'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Password = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleAuthenticatorSecret;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    private array $roles = [];

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'user')]
    private Collection $tikets;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $google_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:collection'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $passwordResetExpiresAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]
    private ?string $Rle = null;

        public function __construct() {
            $this->createdAt = new \DateTimeImmutable();
            $this->roles[]="ROLE_USER ";
            $this->tikets = new ArrayCollection();
	        $this->Rle="U";
 
       }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null; // Utilisez un encodeur sans sel ou gérez le sel directement dans l'encodeur
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Effacez les informations sensibles ici si nécessaire
    }


    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(?string $Password): static
    {
        $this->Password = $Password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->Email;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTikets(): Collection
    {
        return $this->tikets;
    }

    public function addTiket(Ticket $tiket): static
    {
        if (!$this->tikets->contains($tiket)) {
            $this->tikets->add($tiket);
            $tiket->setUser($this);
        }

        return $this;
    }

    public function removeTiket(Ticket $tiket): static
    {
        if ($this->tikets->removeElement($tiket)) {
            // set the owning side to null (unless already changed)
            if ($tiket->getUser() === $this) {
                $tiket->setUser(null);
            }
        }

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->google_id;
    }

    public function setGoogleId(?string $google_id): static
    {
        $this->google_id = $google_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): static
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    public function getPasswordResetExpiresAt(): ?\DateTimeInterface
    {
        return $this->passwordResetExpiresAt;
    }

    public function setPasswordResetExpiresAt(?\DateTimeInterface $passwordResetExpiresAt): static
    {
        $this->passwordResetExpiresAt = $passwordResetExpiresAt;

        return $this;
    }

    public function getRle(): ?string
    {
        return $this->Rle;
    }

    public function setRle(string $Rle): static
    {
        $this->Rle = $Rle;

        return $this;
    }

        
}
