<?php
declare(strict_types=1);

namespace App\Proyectos\Domain;

use App\Proyectos\Domain\ValueObjects\ActividadId;
use App\Proyectos\Infrastructure\Repositories\ActividadRepository;
use App\Usuarios\Domain\Usuario;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: "unique_nombre_proyecto", columns: ["nombre", "proyecto_id"])]
class Actividad
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "ActividadId")]
    private ?ActividadId $id;


    #[ORM\Column(length: 255)]
    private ?string $nombre = null;
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $usuario;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Proyecto $proyecto;


    public function __construct()
    {
        $this->id = ActividadId::create();
    }

    public function getId(): ?ActividadId
    {
        return $this->id;
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getProyecto(): Proyecto
    {
        return $this->proyecto;
    }

    public function setProyecto(Proyecto $proyecto): static
    {
        $this->proyecto = $proyecto;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public static function create(
        ?string             $descripcion,
        ?\DateTimeInterface $fecha,
        ?Usuario            $user,
        ?Proyecto           $proyecto,

    ): self
    {
        $actividad = new self();
        $actividad->setDescripcion($descripcion);
        $actividad->setFecha($fecha);
        $actividad->setUsuario($user);
        $actividad->setProyecto($proyecto);

        return $actividad;
    }
}
