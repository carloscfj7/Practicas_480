<?php
declare(strict_types=1);

namespace App\Consultores\Domain;

use App\Consultores\Domain\ValueObjects\DisponibilidadId;
use App\Consultores\Infrastructure\Repositories\DisponibilidadRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: "unique_ini", columns: ["fecha_ini", "consultor_id"])]
class Disponibilidad
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "DisponibilidadId")]
    private ?DisponibilidadId $id;


    #[ORM\Column]
    private ?bool $disponible = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha_ini = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha_fin = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Consultor $consultor;


    public function __construct()
    {
        $this->id = DisponibilidadId::create();
    }

    public function getId(): ?DisponibilidadId
    {
        return $this->id;
    }

    public function isDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): static
    {
        $this->disponible = $disponible;

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

    public function setFechaFin(\DateTimeInterface $fecha_fin): static
    {
        $this->fecha_fin = $fecha_fin;

        return $this;
    }

    public function getConsultor(): ?Consultor
    {
        return $this->consultor;
    }

    public function setConsultor(Consultor $consultor): static
    {
        $this->consultor = $consultor;

        return $this;
    }

    public static function create(
        bool                $disponible,
        ?\DateTimeInterface $fecha_ini,
        ?\DateTimeInterface $fecha_fin,
        ?Consultor          $user

    ): self
    {
        $disponibilidad = new self();
        $disponibilidad->setDisponible();
        $disponibilidad->setFechaIni($fecha_ini);
        $disponibilidad->setFechaFin($fecha_fin);
        $disponibilidad->setConsultor($user);
        return $disponibilidad;
    }
}
