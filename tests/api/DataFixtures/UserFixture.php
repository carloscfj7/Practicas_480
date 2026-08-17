<?php
namespace App\Tests\api\DataFixtures;

use App\Usuarios\Domain\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Service\Attribute\Required;

class UserFixture extends Fixture
{
    public const USUARIO_1 = 'usuario_1';
    public const USUARIO_2 = 'usuario_2';

    private UserPasswordHasherInterface $passwordHasher;

    #[Required]
    public function setPasswordHasher(UserPasswordHasherInterface $passwordHasher): void
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $usuario = new Usuario();
        $usuario->setEmail("carlos@prueba.com");
        $encodedPassword = $this->passwordHasher->hashPassword($usuario, "contraseña");
        $usuario->setPassword($encodedPassword);
        $usuario->setRoles(["ROLE_ADMIN", "ROLE_CONSULTOR", "ROLE_USER"]);

        $manager->persist($usuario);
        $this->addReference(self::USUARIO_1, $usuario);

        $usuario2 = new Usuario();
        $usuario2->setEmail("carlos2@prueba.com");
        $encodedPassword2 = $this->passwordHasher->hashPassword($usuario2, 'contraseña');
        $usuario2->setPassword($encodedPassword2);
        $usuario2->setRoles(["ROLE_USER", "ROLE_CLIENTE"]);

        $manager->persist($usuario2);
        $this->addReference(self::USUARIO_2, $usuario2);


        $manager->flush();
    }
}
