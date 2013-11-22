<?php
namespace SymfonyContrib\Bundle\VotingBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class VoteRepository extends EntityRepository
{
    protected $em;

    public function __construct($em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->em = $em;
    }

    public function removeVote($id)
    {
        $dql = "DELETE VotingBundle:Vote v
                WHERE v.id = :id";
        $this->em->createQuery($dql)
            ->execute(['id' => $id]);
    }

    public function getVote($id)
    {

    }

    public function getUserVote($key, $voter)
    {
        $dql = "SELECT v
                FROM VotingBundle:Vote v
                WHERE v.key = :key
                    AND v.voter = :voter";

        return $this->em->createQuery($dql)
            ->setParameters([
                'key' => $key,
                'voter' => $voter,
            ])
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
