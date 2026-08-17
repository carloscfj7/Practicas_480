<?php

namespace App\DataFixtures;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\ValueObjects\Nivel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class HabilidadFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $habilidad = new Habilidad();
        $habilidad->setNombre('Python');
        $habilidad->setNivel(Nivel::fromString('alto'));
        $consultor = $manager->getRepository(Consultor::class)->findByEmailUsuario("carlos@prueba.com");

        $habilidad->addConsultor($consultor);

        $manager->persist($habilidad);

        $habilidad2 = new Habilidad();
        $habilidad2->setNombre('Php');
        $habilidad2->setNivel(Nivel::fromString('alto'));
        $habilidad2->addConsultor($consultor);

        $manager->persist($habilidad2);

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
