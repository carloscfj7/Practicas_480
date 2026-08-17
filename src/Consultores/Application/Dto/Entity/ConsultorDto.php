<?php

declare(strict_types=1);

namespace App\Consultores\Application\Dto\Entity;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\ValueObjects\ConsultorId;
use App\Proyectos\Application\Dto\ProyectoSimpleDto;
use App\Proyectos\Application\Dto\TareaSimpleDto;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\Tarea;
use App\Usuarios\Application\Dto\Usuario\Entity\UsuarioDto;
use App\Usuarios\Domain\Usuario;

class ConsultorDto
{
    public ConsultorId $id;
    public string $nombre;
    public string $apellidos;
    public string $nombreCompleto;
    public string $perfil;
    public string $usuario;

    public array $proyectos = [];

    public array $tareas = [];

    public array $habilidades = [];

    public array $disponibilidades = [];
    public  function fromEntity(Consultor $consultor): self
    {
        $dto = new self();

        $dto->id = $consultor->getId();
        $dto->nombre = $consultor->getNombre() ?? '';
        $dto->apellidos = $consultor->getApellidos() ?? '';
        $dto->nombreCompleto = trim($dto->nombre . ' ' . $dto->apellidos);
        $dto->perfil = $consultor->getPerfil()->value;


        $usuarioEntity = $consultor->getUsuario();
        if ($usuarioEntity instanceof Usuario) {
            $dto->usuario = UsuarioDto::fromEntity($usuarioEntity)->email;
        }

        $proyectoDto = new ProyectoSimpleDto();
        foreach ($consultor->getProyectos() as $proyecto) {
            if ($proyecto instanceof Proyecto) {
                $dto->proyectos[] = $proyectoDto->fromEntity($proyecto);
            }
        }
        $tareaDto = new TareaSimpleDto();
        foreach ($consultor->getTareas() as $tarea) {
            if ($tarea instanceof Tarea) {
                $dto->tareas[] = $tareaDto->fromEntity($tarea);
            }
        }
        $habilidadDto = new HabilidadDto();
        foreach ($consultor->getHabilidades() as $habilidad) {
            if ($habilidad instanceof Habilidad) {
                $dto->habilidades[] = $habilidadDto->fromEntity($habilidad);
            }
        }
        $disponibilidadDto = new DisponibilidadDto();
        foreach ($consultor->getDisponibilidades() as $disponibilidad) {
            if ($disponibilidad instanceof Disponibilidad) {
                $dto->disponibilidades[] = $disponibilidadDto->fromEntity($disponibilidad);
            }
        }

        return $dto;
    }

    public  function collectionFromEntities(array $consultores): array
    {
        return array_map(fn(Consultor $c) => self::fromEntity($c), $consultores);
    }
}