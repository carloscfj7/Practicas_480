<?php

namespace App\DataFixtures;

use App\Consultores\Domain\Consultor;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\ValueObjects\Estado;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TareaFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $tarea = new Tarea();
        $tarea->setNombre('Tarea 1');
        $tarea->setDescripcion('Descripcion 1');
        $tarea->setFechaIni(new \DateTime('2025-01-01 10:00:00'));
        $tarea->setFechaFin(new \DateTime('2025-01-02 17:00:00'));
        $tarea->setEstado(Estado::COMPLETADO);
        $proyecto = $manager->getRepository(Proyecto::class)->findByNombre('Proyecto 1');
        $tarea->setProyecto($proyecto);
        $consultor = $manager->getRepository(Consultor::class)->findByEmailUsuario('carlos@prueba.com');

        $tarea->addConsultor($consultor);

        $manager->persist($tarea);

        $tarea2 = new Tarea();
        $tarea2->setNombre('Tarea 2');
        $tarea2->setDescripcion('Descripcion 2');
        $tarea2->setFechaIni(new \DateTime('2025-01-01 10:00:00'));
        $tarea2->setFechaFin(new \DateTime('2025-01-02 17:00:00'));
        $tarea2->setEstado(Estado::COMPLETADO);
        $proyecto2 = $manager->getRepository(Proyecto::class)->findByNombre('Proyecto 2');
        $tarea2->setProyecto($proyecto2);
        $tarea2->addConsultor($consultor);

        $manager->persist($tarea2);

        $manager->flush();
    }

    public function getDependencies():array
    {
        return [
            UserFixture::class,
            ConsultorFixture::class,
            ClienteFixture::class,
            ProyectoFixture::class
        ];
    }
}
