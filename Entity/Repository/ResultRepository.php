<?php
namespace SymfonyContrib\Bundle\VotingBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class ResultRepository extends EntityRepository
{
    protected $em;

    public function __construct($em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->em = $em;
    }

    public function removeResultsByKey($key)
    {
        $dql = "DELETE VotingBundle:Result r
                WHERE r.key = :key";
        $this->em->createQuery($dql)
            ->execute(['key' => $key]);
    }

    public function removeResult($id)
    {
        $dql = "DELETE VotingBundle:Result r
                WHERE r.id = :id";
        $this->em->createQuery($dql)
            ->execute(['id' => $id]);
    }

    public function getResult($id)
    {

    }

    public function getResultByKey($key, $method = null)
    {
        $dql = "SELECT r
                FROM VotingBundle:Result r
                WHERE r.key = :key";
        if ($method) {
            $dql .= " AND r.method = :method";
        }

        return $this->em->createQuery($dql)
            ->setMaxResults(1)
            ->setParameters([
                'key' => $key,
                'method' => $method,
            ])
            ->getOneOrNullResult();

    }

}
