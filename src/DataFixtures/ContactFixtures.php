<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Contact;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ContactFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $contact = new Contact();
            $contact->setFirstname($faker->firstName());
            $contact->setLastname($faker->lastName());
            $contact->setEmail($this->getReference('user_' . rand(11, 13))->getEmail());
            $contact->setMessage($faker->paragraph(4, true));
            $contact->setSubject($faker->words(4, true));
            $contact->setUser($this->getReference('user_' . rand(0, 4)));
            $manager->persist($contact);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
