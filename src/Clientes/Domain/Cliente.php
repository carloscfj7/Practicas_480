<?php

namespace App\Clientes\Domain;

use App\Clientes\Domain\ValueObjects\ClienteId;
use App\Proyectos\Domain\Proyecto;
use App\Clientes\Infrastructure\Repositories\ClienteRepository;
use App\Usuarios\Domain\Usuario;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity]
class Cliente
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "ClienteId")]
    private ?ClienteId $id;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $contacto = null;

    #[ORM\Column(length: 255)]
    private ?string $direccion = null;

    #[ORM\OneToOne(targetEntity: Usuario::class, cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $usuario;


    #[ORM\OneToMany(mappedBy: "cliente", targetEntity: Proyecto::class)]
    private Collection $proyectos;


    public function __construct()
    {
        $this->id = ClienteId::create();
    }

    public function getId(): ?ClienteId
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getContacto(): ?string
    {
        return $this->contacto;
    }

    public function setContacto(string $contacto): static
    {
        $this->contacto = $contacto;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }
    public function getIdUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setIdUsuario(Usuario $id_usuario): static
    {
        $this->usuario = $id_usuario;

        return $this;
    }

    public function getProyectos(): Collection
    {
        return $this->proyectos;
    }

    public function setProyectos(Collection $proyectos): self
    {
        $this->proyectos = $proyectos;
        return $this;
    }

    public static function create(
        ?Usuario $user,
        ?string  $nombre,
        ?string  $contacto,
        ?string  $direccion,
    ): self
    {
        $cliente = new self();
        $cliente->setIdUsuario($user);
        $cliente->setNombre($nombre);
        $cliente->setContacto($contacto);
        $cliente->setDireccion($direccion);
        return $cliente;
    }
}
