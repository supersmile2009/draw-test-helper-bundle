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

        foreach($doctrine->getRepository($class)->findBy($criteria) as $result) {
            $manager->remove($result);
        }

        $manager->flush();

        $kernel->shutdown();
        $kernel->boot();
    }
}