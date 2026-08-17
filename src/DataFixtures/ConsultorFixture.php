<?php

namespace App\DataFixtures;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ValueObjects\Perfil;
use App\Usuarios\Domain\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Logging\Exception;

class ConsultorFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $consultor = new Consultor();
        $consultor->setNombre('Carlos');
        $consultor->setApellidos('Flores');


        $usuario = $manager->getRepository(Usuario::class)->findByEmail('carlos@prueba.com');
        if (!$usuario) {
            throw new \Exception('Usuarios no encontrado 1' );
        }

        $consultor->setUsuario($usuario);

        $consultor->setPerfil(Perfil::PROJECT_MANAGER);
        $this->addReference('consultor_1', $consultor);

        $manager->persist($consultor);
        $manager->flush();

    }

    public function getDependencies():array
    {
        return [
            UserFixture::class,
        ];
    }
}
