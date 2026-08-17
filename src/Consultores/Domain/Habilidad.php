<?php
declare(strict_types=1);

namespace App\Consultores\Domain;

use App\Consultores\Domain\ValueObjects\HabilidadId;
use App\Consultores\Domain\ValueObjects\Nivel;
use App\Consultores\Infrastructure\Repositories\HabilidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: "unique_nombre", columns: ["nombre", "nivel"])]
class Habilidad
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "HabilidadId")]
    private ?HabilidadId $id;


    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?Nivel $nivel = null;

    #[ORM\ManyToMany(targetEntity: Consultor::class, inversedBy: 'habilidades')]
    private Collection $consultores;

    public function __construct()
    {
        $this->id = HabilidadId::create();
        $this->consultores = new ArrayCollection();
    }

    public function getId(): ?HabilidadId
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

    public function removeConsultor(Consultor $consultor): static
    {
        $this->consultores->removeElement($consultor);

        return $this;
    }

    /**
     * @param string|null $nivel
     */
    public function setNivel(Nivel $nivel): void
    {
        $this->nivel = $nivel;
    }

    /**
     * @return string|null
     */
    public function getNivel(): ?Nivel
    {
        return $this->nivel;
    }

    public static function create(
        ?string $nombre,
        ?string $nivel,

    ): self
    {
        $habilidad = new self();
        $habilidad->setNombre($nombre);
        $habilidad->setNivel($nivel);
        return $habilidad;
    }
}
