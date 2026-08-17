<?php
declare(strict_types=1);

namespace App\Proyectos\Domain;

use App\Clientes\Domain\Cliente;
use App\Consultores\Domain\Consultor;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Proyectos\Domain\ValueObjects\ProyectoId;
use App\Proyectos\Infrastructure\Repositories\ProyectoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Proyecto
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "ProyectoId")]
    private ?ProyectoId $id;


    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha_ini = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_fin = null;

    #[ORM\Column(length: 255)]
    private ?Estado $estado = null;

    #[ORM\ManyToOne(targetEntity: Cliente::class)]
    #[ORM\JoinColumn(name: "cliente", referencedColumnName: "id")]
    private ?Cliente $cliente = null;

    #[ORM\ManyToMany(targetEntity: Consultor::class, inversedBy: 'proyectos')]
    #[ORM\JoinTable(name: "consultor_proyecto")]
    private Collection $consultores;

    public function __construct()
    {
        $this->id = ProyectoId::create();
        $this->consultores = new ArrayCollection();
    }

    public function getId(): ?ProyectoId
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFechaIni(): ?\DateTimeInterface
    {
        return $this->fecha_ini;
    }

    public function setFechaIni(\DateTimeInterface $fecha_ini): static
    {
        $this->fecha_ini = $fecha_ini;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fecha_fin;
    }

    public function setFechaFin(?\DateTimeInterface $fecha_fin): static
    {
        $this->fecha_fin = $fecha_fin;

        return $this;
    }

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(Estado $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): static
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * @return Collection<int, Consultor>
     */
    public function getConsultores(): Collection
    {
        return $this->consultores;
    }

    public function addConsultor(Consultor $consultor): static
    {
        if (!$this->consultores->contains($consultor)) {
            $this->consultores->add($consultor);
        }

        return $this;
    }

    public function removeConsultor(Consultor $consultor): static
    {
        $this->consultores->removeElement($consultor);

        return $this;
    }

    public static function create(
        ?string             $nombre,
        ?string             $descripcion,
        ?\DateTimeInterface $fecha_ini,
        ?\DateTimeInterface $fecha_fin,
        ?Estado             $estado,
        ?Cliente            $user,

    ): self
    {
        $proyecto = new self();
        $proyecto->setNombre($nombre);
        $proyecto->setDescripcion($descripcion);
        $proyecto->setFechaIni($fecha_ini);
        $proyecto->setFechaFin($fecha_fin);
        $proyecto->setEstado($estado);
        $proyecto->setCliente($user);
        return $proyecto;
    }
}
