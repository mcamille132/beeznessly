<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Download;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class DownloadFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $download = new Download();
            $download->setUser($this->getReference('user_' . rand(11, 13)));
            $download->setEbook($this->getReference('ebook_' . rand(0, 19)));
            $manager->persist($download);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array (
            UserFixtures::class,
            EbookFixtures::class,
        );
    }
}
