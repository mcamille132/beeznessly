<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Faker;
use App\Service\SlugifyService;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $encoder;
    private $slugifyService;

    public function __construct(UserPasswordEncoderInterface $encoder, SlugifyService $slugifyService)
    {
        $this->encoder = $encoder;
        $this->slugifyService = $slugifyService;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setEmail('user' . $i . '@expert.com');
            $user->setPassword($this->encoder->encodePassword($user, 'password'));
            $user->setRoles(['ROLE_EXPERT']);
            $user->setDescription($faker->paragraph());
            $user->setPhone($faker->randomNumber(9));
            $user->setCompanyName($faker->words(2, true));
            $user->setSiretNumber('80244211100042');
            $user->setIsValidated(true);
            $user->setTown($faker->city());
            $user->setZipcode($faker->randomNumber(5));
            $user->setAdress($faker->address());
            $user->setSlug($this->slugifyService->generate($user->getCompanyName()));
            for ($j = 0; $j < 3; $j++) {
                $user->addExpertise($this->getReference('expertise_' . rand(0, 5)));
            }
            $user->setProvider($this->getReference('provider_' . rand(0, 3)));
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }
        for ($i = 5; $i < 10; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setEmail('user' . $i . '@expert.com');
            $user->setPassword($this->encoder->encodePassword($user, 'password'));
            $user->setRoles(['ROLE_EXPERT']);
            $user->setDescription($faker->paragraph());
            $user->setPhone($faker->randomNumber(9));
            $user->setCompanyName($faker->words(2, true));
            $user->setSiretNumber('80244211100042');
            $user->setIsValidated(false);
            $user->setTown($faker->city());
            $user->setZipcode($faker->randomNumber(5));
            $user->setAdress($faker->address());
            $user->setSlug($this->slugifyService->generate($user->getCompanyName()));
            for ($j = 0; $j < 3; $j++) {
                $user->addExpertise($this->getReference('expertise_' . rand(0, 5)));
            }
            $user->setProvider($this->getReference('provider_' . rand(0, 3)));
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }
        for ($i = 10; $i < 11; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setEmail('user' . $i . '@admin.com');
            $user->setPassword($this->encoder->encodePassword($user, 'password'));
            $user->setRoles(['ROLE_ADMIN']);
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }
        for ($i = 11; $i < 14; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setEmail('user' . $i . '@entrepreneur.com');
            $user->setPassword($this->encoder->encodePassword($user, 'password'));
            $user->setRoles(['ROLE_ENTREPRENEUR']);
            $user->setIsValidated(true);
            $user->setPhone($faker->randomNumber(9));
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array (
            ProviderFixtures::class,
            ExpertiseFixtures::class,
        );
    }
}
