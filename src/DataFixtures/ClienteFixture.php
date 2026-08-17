<?php

namespace App\DataFixtures;

use App\Clientes\Domain\Cliente;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\Email;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Logging\Exception;

class ClienteFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $cliente = new Cliente();
        $cliente->setNombre('Cliente 2');
        $cliente->setContacto('Contacto 2');
        $cliente->setDireccion('Direccion 2');
        $usuario = $manager->getRepository(Usuario::class)->findByEmail(new Email('carlos2@prueba.com'));
        if (!$usuario) {
            throw new \Exception('Usuarios no encontrado 2' );
        }
        $cliente->setIdUsuario($usuario);

        $manager->persist($cliente);

        $manager->flush();
    }


    public function getDependencies():array
    {
        return [
            UserFixture::class,
        ];
    }
}
