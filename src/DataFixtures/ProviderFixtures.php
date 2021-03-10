<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Provider;

class ProviderFixtures extends Fixture
{
    protected const PROVIDERS = [
        'Agence digitale',
        'Freelance',
        'Centre de formation',
        'Incubateur',
    ];
    public function load(ObjectManager $manager)
    {
        foreach (self::PROVIDERS as $key => $providerType) {
            $provider = new Provider();
            $provider->setType($providerType);
            $manager->persist($provider);
            $this->addReference('provider_' . $key, $provider);
        }

        $manager->flush();
    }
}
