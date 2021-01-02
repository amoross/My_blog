<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticlesFixtures extends Fixture
{
    public function load(ObjectManager $manager){

        //creer 3 fack  category
        $faker = \Faker\Factory::create();
        for ($i=0; $i<=3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                ->setDescription($faker->paragraph());

            $manager->persist($category);

            //creer en 4 et 6 articles

            for ($j = 0; $j <= mt_rand(4,6); $j++) {
                $content= '<p>'. join($faker->paragraphs(5), '</p> <p>') . '</p>';

                $article = new Article();
                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category)
                    ->setUsers(null);

                $manager->persist($article);


                //Commentaire

                for ($k=0; $k<= mt_rand(4,10);$k++) {
                    $content = '<p>' . join($faker->paragraphs(2), '</p> <p>') . '</p>';

                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;

                    $minimum = '-' . $days . 'day';

                    $comment = new Comment();
                    $comment->setAuthor($faker->name)
                        ->setText($content)
                        ->setCreatedAt($faker->dateTimeBetween($minimum))
                        ->setArticle($article);

                    $manager->persist($comment);
                }

            }
        }
        $manager->flush();
    }

}
