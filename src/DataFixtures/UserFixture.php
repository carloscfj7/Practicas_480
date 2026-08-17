<?php

namespace App\DataFixtures;

use App\Usuarios\Application\Exceptions\Usuario\InvalidEmailException;
use App\Usuarios\Domain\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }
    /**
     * @throws InvalidEmailException
     */
    public function load(ObjectManager $manager): void
    {
        $usuario = new Usuario();
        $usuario->setEmail("carlos@prueba.com");


        $encodedPassword = $this->passwordHasher->hashPassword($usuario,"contraseña");
        $usuario->setPassword($encodedPassword);
        $usuario->setRoles(["ROLE_CONSULTOR", "ROLE_USER"]);

        $manager->persist($usuario);

        $usuario2 = new Usuario();
        $usuario2->setEmail("carlos2@prueba.com");

        $encodedPassword2 = $this->passwordHasher->hashPassword($usuario2,'contraseña');
        $usuario2->setPassword($encodedPassword2);
        $usuario2->setRoles(["ROLE_USER","ROLE_CLIENTE"]);

        $manager->persist($usuario2);

        $manager->flush();

    }


}
