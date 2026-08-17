<?php

namespace App\Tests\api\DataFixtures;

use App\Clientes\Domain\Cliente;
use App\Usuarios\Domain\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ClienteFixture extends Fixture implements DependentFixtureInterface
{
    public const CLIENTE_REF = 'cliente_demo';

    public function load(ObjectManager $manager): void
    {
        /** @var Usuario $usuario */
        $usuario = $this->getReference(UserFixture::USUARIO_2);

        if (!$usuario) {
            throw new \Exception('Usuario no encontrado: carlos2@prueba.com');
        }

        $cliente = new Cliente();
        $cliente->setNombre('Cliente Test');
        $cliente->setContacto('Contacto Test');
        $cliente->setDireccion('Dirección Test');
        $cliente->setIdUsuario($usuario);

        $manager->persist($cliente);
        $manager->flush();

        $this->addReference(self::CLIENTE_REF, $cliente);
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
