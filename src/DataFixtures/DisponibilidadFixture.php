<?php

namespace App\DataFixtures;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\Disponibilidad;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Logging\Exception;

class DisponibilidadFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $consultor = $manager->getRepository(Consultor::class)->findByEmailUsuario('carlos@prueba.com');
        if (!$consultor) {
            throw new \Exception('Consultor no encontrado');
        }


        $disponibilidad = new Disponibilidad();
        $disponibilidad->setDisponible(true);
        $disponibilidad->setFechaIni(new \DateTime('2025-04-01'));
        $disponibilidad->setFechaFin(new \DateTime('2025-04-30'));
        $disponibilidad->setConsultor($consultor);

        $manager->persist($disponibilidad);

        if (!$consultor) {
            throw new \Exception('Consultor no encontrado');
        }

        $disponibilidad2 = new Disponibilidad();
        $disponibilidad2->setDisponible(false);
        $disponibilidad2->setFechaIni(new \DateTime('2025-05-01'));
        $disponibilidad2->setFechaFin(new \DateTime('2025-05-15'));
        $disponibilidad2->setConsultor($consultor);

        $manager->persist($disponibilidad2);

        $manager->flush();
    }

    public function getDependencies():array
    {
        return [
            UserFixture::class,
            ConsultorFixture::class
        ];
    }

}
