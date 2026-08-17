<?php

namespace App\Usuarios\Domain;

use App\Clientes\Domain\Cliente;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Usuarios\Domain\ValueObjects\NotificacionId;
use App\Usuarios\Infrastructure\Repositories\NotificacionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Notificacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "NotificacionId")]
    private ?NotificacionId $id;

    #[ORM\Column(length: 255)]
    private ?string $mensaje = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\ManyToMany(targetEntity: Usuario::class, inversedBy: 'notificaciones')]
    private Collection $usuarios;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Usuario $creador = null;


    public function __construct()
    {
        $this->id = NotificacionId::create();
        $this->usuarios = new ArrayCollection();
    }

    public function getId(): ?NotificacionId
    {
        return $this->id;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): static
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * @return Collection<int, Usuario>
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): static
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios->add($usuario);
        }

        return $this;
    }


    public function removeUsuario(Usuario $usuario): static
    {
        $this->usuarios->removeElement($usuario);

        return $this;
    }

    public function getCreador(): ?Usuario
    {
        return $this->creador;
    }

    public function setCreador(?Usuario $creador): static
    {
        $this->creador = $creador;

        return $this;
    }

    public static function create(
        ?string             $mensaje,
        ?\DateTimeInterface $fecha,
        ?Usuario            $creador,
        ?Collection         $usuarios,
    ): self
    {
        $notificacion = new self();
        $notificacion->setMensaje($mensaje);
        $notificacion->setFecha($fecha);
        $notificacion->setCreador($creador);
        foreach ($usuarios as $usuario) {
            $notificacion->addUsuario($usuario);
        }
        return $notificacion;
    }
}
