<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberClosedTickets;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberReopenedTickets;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="assignedTo")
     */
    private $tickets;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="createdBy", orphanRemoval=true)
     */
    private $ticketsCreated;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->createdTickets = new ArrayCollection();
        $this->ticketsCreated = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getNumberClosedTickets(): ?int
    {
        return $this->numberClosedTickets;
    }

    public function setNumberClosedTickets(?int $numberClosedTickets): self
    {
        $this->numberClosedTickets = $numberClosedTickets;

        return $this;
    }

    public function getNumberReopenedTickets(): ?int
    {
        return $this->numberReopenedTickets;
    }

    public function setNumberReopenedTickets(?int $numberReopenedTickets): self
    {
        $this->numberReopenedTickets = $numberReopenedTickets;

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setAssignedTo($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getAssignedTo() === $this) {
                $ticket->setAssignedTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTicketsCreated(): Collection
    {
        return $this->ticketsCreated;
    }

    public function addTicketsCreated(Ticket $ticketsCreated): self
    {
        if (!$this->ticketsCreated->contains($ticketsCreated)) {
            $this->ticketsCreated[] = $ticketsCreated;
            $ticketsCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTicketsCreated(Ticket $ticketsCreated): self
    {
        if ($this->ticketsCreated->removeElement($ticketsCreated)) {
            // set the owning side to null (unless already changed)
            if ($ticketsCreated->getCreatedBy() === $this) {
                $ticketsCreated->setCreatedBy(null);
            }
        }

        return $this;
    }
}
