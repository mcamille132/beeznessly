<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Expertise;

class ExpertiseFixtures extends Fixture
{
    protected const EXPERTISES = [
        'Communication',
        'Marketing',
        'Business Analysis',
        'Developpement web',
        'SEO',
        'Graphisme'
    ];
    public function load(ObjectManager $manager)
    {
        foreach (self::EXPERTISES as $key => $type) {
            $expertise = new Expertise();
            $expertise->setName($type);
            $manager->persist($expertise);
            $this->addReference('expertise_' . $key, $expertise);
        }
        $manager->flush();
    }
}
