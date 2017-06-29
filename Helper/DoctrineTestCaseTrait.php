<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

trait DoctrineTestCaseTrait
{
    use ServiceTestCaseTrait;

    public static function findByForDelete($class, $criteria)
    {
        $kernel = static::getSharedKernel('delete');

        $doctrine = $kernel->getContainer()->get("doctrine");
        $manager = $doctrine->getManagerForClass($class);

        if($criteria) {
            $entities = $doctrine->getRepository($class)->findBy($criteria);
        } else {
            $entities =$doctrine->getRepository($class)->findAll();
        }

        foreach($entities as $result) {
            $manager->remove($result);
        }

        $manager->flush();
    }
}