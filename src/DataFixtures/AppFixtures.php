<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

            //create user

            $users = [];

            for($i = 0; $i < 10; $i++) {
                $user = new User();

                $pictureId = $faker->numberBetween(1,99) . '.jpg';
                $picture = 'https://randomuser.me/api/portraits/men/' . $pictureId;

                $hash = $this->encoder->encodePassword($user, 'password');

                $user->setFirstname($faker->firstname('male'))
                    ->setLastName($faker->lastname('male'))
                    ->setEmail($faker->email('male'))
                    ->setIntroduction($faker->sentence())
                    ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                    ->setHash($hash)
                    ->setPicture($picture);

                    $manager->persist($user);
                    $users[] = $user;
            }

        //create Ad
        for($i=0; $i < 20 ; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);

                for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                    $image = new Image();

                    $image->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence())
                        ->setAd($ad);

                        $manager->persist($image);
                }

                $manager->persist($ad);


        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
