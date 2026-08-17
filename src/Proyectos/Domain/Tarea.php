<?php
declare(strict_types=1);

namespace App\Proyectos\Domain;

use App\Consultores\Domain\Consultor;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Proyectos\Domain\ValueObjects\TareaId;
use App\Proyectos\Infrastructure\Repositories\TareaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: "unique_nombre_proyecto", columns: ["nombre", "proyecto_id"])]
class Tarea
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "TareaId")]
    private ?TareaId $id;


    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $fecha_ini = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $fecha_fin = null;



    #[ORM\Column(length: 255)]
    private string|null $estimacion = null;

    #[ORM\Column(length: 255)]
    private ?Estado $estado = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Proyecto $proyecto = null;

    #[ORM\ManyToMany(targetEntity: Consultor::class, inversedBy: 'tareas')]
    #[ORM\JoinTable(name: "consultor_tarea")]
    private Collection $consultores;

    public function __construct()
    {
        $this->id = TareaId::create();
        $this->consultores = new ArrayCollection();
    }

    public function getId(): ?TareaId
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

    public function getEstimacion(): string
    {
        return $this->estimacion;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setEstimacion(): static
    {
        if (!$this->fecha_fin instanceof \DateTime || !$this->fecha_ini instanceof \DateTime) {
            return $this;
        }
        $estimacion = $this->fecha_fin->diff($this->fecha_ini);
        $this->estimacion = $estimacion->format('%dd %hh %mm');
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

    public function getProyecto(): ?Proyecto
    {
        return $this->proyecto;
    }

    public function setProyecto(?Proyecto $proyecto): static
    {
        $this->proyecto = $proyecto;

        return $this;
    }

    /**
     * @return Collection<int, Consultor>
     */
    public function getConsultores(): Collection
    {
        return $this->consultores;
    }

    public function addConsultor(Consultor $consultore): static
    {
        if (!$this->consultores->contains($consultore)) {
            $this->consultores->add($consultore);
        }

        return $this;
    }

    public function removeConsultor(Consultor $consultore): static
    {
        $this->consultores->removeElement($consultore);

        return $this;
    }

    public function getFechaFin(): ?\DateTime
    {
        return $this->fecha_fin;
    }

    public function getFechaIni(): ?\DateTime
    {
        return $this->fecha_ini;
    }

    public function setFechaFin(?\DateTime $fecha_fin): void
    {
        $this->fecha_fin = $fecha_fin;
    }

    public function setFechaIni(?\DateTime $fecha_ini): void
    {
        $this->fecha_ini = $fecha_ini;
    }
}
