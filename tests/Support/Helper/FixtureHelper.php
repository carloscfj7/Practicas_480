<?php

namespace App\Tests\Support\Helper;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Codeception\Module;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FixtureHelper extends Module
{
    private ?ReferenceRepository $referenceRepository = null;

    public function loadFixtures(array $fixtures): void
    {
        $container = $this->getModule('Symfony')->_getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        $loader = new Loader();

        foreach ($fixtures as $fixtureClass) {
            $fixture = $container->get($fixtureClass);
            $loader->addFixture($fixture);
        }

        $executor = new ORMExecutor($em, new ORMPurger());
        $this->referenceRepository = $executor->getReferenceRepository();
        $executor->execute($loader->getFixtures());
    }

    public function grabFixtureReference(string $name): mixed
    {
        if (!$this->referenceRepository instanceof \Doctrine\Common\DataFixtures\ReferenceRepository) {
            throw new \RuntimeException("No fixture references loaded. Did you call loadFixtures()?");
        }

        return $this->referenceRepository->getReference($name);
    }
}
