<?php

namespace App\Tests\api\DataFixtures;

use App\Clientes\Domain\Cliente;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ValueObjects\Estado;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Logging\Exception;

class ProyectoFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $proyecto = new Proyecto();
        $proyecto->setNombre('Proyecto 1');
        $proyecto->setDescripcion('Descripción del proyecto 1');
        $proyecto->setFechaIni(new \DateTime('2023-01-01'));
        $proyecto->setFechaFin(new \DateTime('2023-12-31'));
        $proyecto->setEstado(Estado::COMPLETADO);
        $cliente = $manager->getRepository(Cliente::class)->findByEmailUsuario("carlos2@prueba.com");
        $proyecto->setCliente($cliente);
        $consultor = $this->getReference('consultor_1');


        $proyecto->addConsultor($consultor);


        $manager->persist($proyecto);


        $proyecto2 = new Proyecto();
        $proyecto2->setNombre('Proyecto 2');
        $proyecto2->setDescripcion('Descripción del proyecto 2');
        $proyecto2->setFechaIni(new \DateTime('2025-01-01'));
        $proyecto2->setFechaFin(new \DateTime('2025-04-15'));
        $proyecto2->setEstado(Estado::EN_PROCESO);
        $proyecto2->setCliente($cliente);

        $proyecto2->addConsultor($consultor);

        $manager->persist($proyecto2);

        $manager->flush();

    }

    public function getDependencies():array
    {
        return [
            UserFixture::class,
            ConsultorFixture::class,
            ClienteFixture::class
        ];
    }
}
