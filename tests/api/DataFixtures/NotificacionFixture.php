<?php

namespace App\Tests\api\DataFixtures;

use App\Usuarios\Domain\Notificacion;
use App\Usuarios\Domain\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NotificacionFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $notificacion = new Notificacion();
        $notificacion->setMensaje('Mensaje 1');
        $notificacion->setFecha(new \DateTime('2023-01-01'));
        $usuario = $manager->getRepository(Usuario::class)->findByEmail("carlos@prueba.com");
        $notificacion->addUsuario($usuario);

        $manager->persist($notificacion);

        $notificacion2 = new Notificacion();
        $notificacion2->setMensaje('Mensaje 2');
        $notificacion2->setFecha(new \DateTime('2023-01-02'));
        $notificacion2->addUsuario($usuario);

        $manager->persist($notificacion2);

        $notificacion3 = new Notificacion();
        $notificacion3->setMensaje('Mensaje 1');
        $notificacion3->setFecha(new \DateTime('2023-01-01'));
        $usuario2 = $manager->getRepository(Usuario::class)->findByEmail("carlos2@prueba.com");
        $notificacion3->addUsuario($usuario2);

        $manager->persist($notificacion3);

        $manager->flush();
    }
}
