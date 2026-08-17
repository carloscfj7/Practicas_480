<?php
declare(strict_types=1);

namespace App\Consultores\Domain;

use App\Consultores\Domain\ValueObjects\ConsultorId;
use App\Consultores\Domain\ValueObjects\Perfil;
use App\Consultores\Infrastructure\Repositories\ConsultorRepository;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\Tarea;
use App\Usuarios\Domain\Usuario;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Consultor
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "ConsultorId")]
    private ?ConsultorId $id;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidos = null;

    #[ORM\OneToOne(targetEntity: Usuario::class, cascade: ["remove"])]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $usuario;

    #[ORM\Column]
    private Perfil $perfil;

    #[ORM\ManyToMany(targetEntity: Proyecto::class, mappedBy: 'consultores')]
    #[ORM\JoinTable(name: "consultores_proyectos")]
    private Collection $proyectos;

    #[ORM\ManyToMany(targetEntity: Tarea::class, mappedBy: 'consultores')]
    #[ORM\JoinTable(name: "consultores_tareas")]
    private Collection $tareas;

    #[ORM\ManyToMany(targetEntity: Habilidad::class, mappedBy: 'consultores')]
    private Collection $habilidades;

    #[ORM\OneToMany(mappedBy: "consultor", targetEntity: Disponibilidad::class, cascade: ["persist", "remove"])]
    private Collection $disponibilidades;


    public function __construct()
    {
        $this->id = ConsultorId::create();
        $this->proyectos = new ArrayCollection();
        $this->tareas = new ArrayCollection();
        $this->habilidades = new ArrayCollection();
        $this->disponibilidades = new ArrayCollection();
    }

    public function getId(): ?ConsultorId
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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): static
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getPerfil(): Perfil
    {
        return $this->perfil;
    }

    public function setPerfil(Perfil $perfil): static
    {
        $this->perfil = $perfil;

        return $this;
    }

    /**
     * @return Collection<int, Proyecto>
     */
    public function getProyectos(): Collection
    {
        return $this->proyectos;
    }

    public function addProyecto(Proyecto $proyecto): static
    {
        if (!$this->proyectos->contains($proyecto)) {
            $this->proyectos->add($proyecto);
            $proyecto->addConsultor($this);
        }

        return $this;
    }

    public function removeProyecto(Proyecto $proyecto): static
    {
        if ($this->proyectos->removeElement($proyecto)) {
            $proyecto->removeConsultor($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tarea>
     */
    public function getTareas(): Collection
    {
        return $this->tareas;
    }

    public function addTarea(Tarea $tarea): static
    {
        if (!$this->tareas->contains($tarea)) {
            $this->tareas->add($tarea);
            $tarea->addConsultor($this);
        }

        return $this;
    }

    public function removeTarea(Tarea $tarea): static
    {
        if ($this->tareas->removeElement($tarea)) {
            $tarea->removeConsultor($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Habilidad>
     */
    public function getHabilidades(): Collection
    {
        return $this->habilidades;
    }

    public function addHabilidad(Habilidad $habilidade): static
    {
        if (!$this->habilidades->contains($habilidade)) {
            $this->habilidades->add($habilidade);
            $habilidade->addConsultor($this);
        }

        return $this;
    }

    public function removeHabilidad(Habilidad $habilidade): static
    {
        if ($this->habilidades->removeElement($habilidade)) {
            $habilidade->removeConsultor($this);
        }

        return $this;
    }

    public function getDisponibilidades(): Collection
    {
        return $this->disponibilidades;
    }

    public static function create(
        ?Usuario $user,
        ?string  $nombre,
        ?string  $apellidos,
        ?Perfil  $perfil,
    ): self
    {
        $consultor = new self();
        $consultor->setUsuario($user);
        $consultor->setNombre($nombre);
        $consultor->setApellidos($apellidos);
        $consultor->setPerfil($perfil);
        return $consultor;
    }
}
