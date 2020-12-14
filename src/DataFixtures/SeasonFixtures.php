<?php


namespace App\DataFixtures;

use Faker;
use App\Entity\Season;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        for ($i = 0; $i < 50; $i++) {
            $faker = Faker\Factory::create('enUS');
            $season = new Season();
            $season->setNumber($faker->numberBetween(1, 10));
            $season->setDescription($faker->text);
            $season->setYear($faker->year);
            $season->setDescription($faker->text);
            $season->setProgram($this->getReference('program' . rand(0, 5)));
            $this->addReference('season_' . $i, $season);
            $manager->persist($season);
        }
        $manager->flush();
    }

        public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}