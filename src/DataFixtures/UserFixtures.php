<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;

class UserFixtures extends Fixture
{
    const PASSWORD_USER = "password";
    private $passwordEncoder;

    /**
     * @var Generator
     */
    protected $faker;


    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        $user = new User();
        $user->setEmail('illia.test@gmail.com');
        $user->setFirstName('Illia');
        $user->setLastName('Serediuk');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            self::PASSWORD_USER
        )
        );
        $user->setStatus(User::STATUS_INACTIVE);
        $user->setEnabled(false);
        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) {
            $firstName = $this->faker->firstName;
            $lastName = $this->faker->lastName;
            $user = new User();
            $user->setEmail(mb_strtolower($firstName) . "." . mb_strtolower($lastName) . "@gmail.com");
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                self::PASSWORD_USER
            )
            );
            $user->setStatus(User::STATUS_INACTIVE);
            $user->setEnabled(false);
            $manager->persist($user);
        }

        $manager->flush();
    }
}