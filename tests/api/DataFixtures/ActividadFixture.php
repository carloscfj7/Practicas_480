<?php

namespace App\Tests\api\DataFixtures;

use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\Proyecto;
use App\Usuarios\Domain\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Logging\Exception;

class ActividadFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $actividad = new Actividad();
        $actividad->setNombre('Actividad 1');
        $actividad->setDescripcion('Descripcion 1');
        $actividad->setFecha(new \DateTime('2023-01-01'));
        $proyecto = $manager->getRepository(Proyecto::class)->findByNombre('Proyecto 1');
        $usuario = $manager->getRepository(Usuario::class)->findByEmail('carlos@prueba.com');
        $actividad->setProyecto($proyecto);
        $actividad->setUsuario($usuario);

        $manager->persist($actividad);

        $actividad2 = new Actividad();
        $actividad2->setNombre('Actividad 2');
        $actividad2->setDescripcion('Descripcion 1');
        $actividad2->setFecha(new \DateTime('2023-01-01'));
        $actividad2->setProyecto($proyecto);
        $actividad2->setUsuario($usuario);

        $manager->persist($actividad2);

        $proyecto2 = $manager->getRepository(Proyecto::class)->findByNombre('Proyecto 2');
        $usuario2 = $manager->getRepository(Usuario::class)->findByEmail('carlos@prueba.com');

        $actividad3 = new Actividad();
        $actividad3->setNombre('Actividad 3');
        $actividad3->setDescripcion('Descripcion 1');
        $actividad3->setFecha(new \DateTime('2023-01-01'));
        $actividad3->setProyecto($proyecto2);
        $actividad3->setUsuario($usuario2);

        $manager->persist($actividad3);

        $manager->flush();
    }
    public function getDependencies():array
    {
        return [
            UserFixture::class,
            ProyectoFixture::class
        ];
    }
}
