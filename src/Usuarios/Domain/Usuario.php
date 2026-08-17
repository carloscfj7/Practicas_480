<?php

namespace App\Usuarios\Domain;

use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Domain\Model\UserId;
use App\Usuarios\Infrastructure\Repositories\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "UserId")]
    private ?UserId $id;

    #[ORM\Column(type:"Email", length: 180, unique: true)]
    private ?Email $email;
    #[ORM\Column]
    private array $roles = [];


    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Notificacion::class, mappedBy: 'usuarios')]
    private Collection $notificaciones;


    public function __construct()
    {
        $this->id = UserId::create();
        $this->notificaciones = new ArrayCollection();
    }
    public function getId(): ?UserId
    {
        return $this->id;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getUsername(): ?Email
    {
        return $this->email;
    }


    public function setEmail(string $email): static
    {
        $this->email = new Email($email);
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Notificacion>
     */
    public function getNotificaciones(): Collection
    {
        return $this->notificaciones;
    }

    public function addNotificaciones(Notificacion $notificacione): static
    {
        if (!$this->notificaciones->contains($notificacione)) {
            $this->notificaciones->add($notificacione);
            $notificacione->addUsuario($this);
        }

        return $this;
    }

    public function removeNotificaciones(Notificacion $notificacione): static
    {
        if ($this->notificaciones->removeElement($notificacione)) {
            $notificacione->removeUsuario($this);
        }

        return $this;
    }


    public static function create(
        ?Email $email,
        ?string $password,
        ?array $roles,
    ): self {
        $notificacion = new self();
        $notificacion->setEmail($email);
        $notificacion->setPassword($password);
        $notificacion->setRoles($roles);
        return $notificacion;
    }
}
