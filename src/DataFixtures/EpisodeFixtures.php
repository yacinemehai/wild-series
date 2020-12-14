<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 200; $i++) {
            $faker = Faker\Factory::create('enUs');
            $episode = new Episode();
            $episode->setNumber($faker->numberBetween(1, 20));
            $episode->setSeason($this->getReference('season_' . rand(0, 49)));
            $episode->setTitle($faker->text($maxNbChars = rand(20, 50)));
            $episode->setSynopsis($faker->text(200));
            $manager->persist($episode);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
