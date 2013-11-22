<?php

namespace SymfonyContrib\Bundle\VotingBundle;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;
use SymfonyContrib\Bundle\VotingBundle\Entity\Vote;
use SymfonyContrib\Bundle\VotingBundle\Entity\Result;

class Voting
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface;
     */
    public $sc;


    public function __construct(EntityManager $em, SecurityContextInterface $sc)
    {
        $this->em = $em;
        $this->sc = $sc;
    }

    /**
     * Gets voter identification string.
     *
     * @return string
     */
    public function whoami()
    {
        // @todo: Make this configurable.
        if ($this->sc->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->sc->getToken()->getUser()->getId();
        }

        // @todo: Make this configurable.
        return 'anon';
    }

    /**
     * Gets voter IP address.
     *
     * @return string
     */
    public function getIp()
    {
        // @todo Enhance for proxies.
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get currently logged in user's vote.
     *
     * @param $key
     *
     * @return Vote|null
     */
    public function getMyVote($key)
    {
        $voteRepo = $this->em->getRepository('SymfonyContrib\Bundle\VotingBundle\Entity\Vote');
        $voter = $this->whoami();
        return $voteRepo->getUserVote($key, $voter);
    }

    /**
     * Adds a vote to the system.
     *
     * @param array $data
     * @param bool $updateResults
     */
    public function addVote(array $data, $updateResults = true)
    {
        // Set defaults.
        $data['voter'] = isset($data['voter']) ? $data['voter'] : $this->whoami();
        $data['ip']    = isset($data['ip'])    ? $data['ip']    : $this->getIp();

        // Ensure no ID is set.
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $vote = new Vote($data);

        // @todo Validate vote.

        $this->em->persist($vote);
        $this->em->flush();

        if ($updateResults) {
            $this->updateResults($data['key']);
        }
    }

    /**
     * Gets the sum of votes.
     *
     * @param $key
     *
     * @return Result|null
     */
    public function getSum($key)
    {
        $resultRepo = $this->em->getRepository('SymfonyContrib\Bundle\VotingBundle\Entity\Result');
        return $resultRepo->getResultByKey($key, 'sum');
    }

    /**
     * Gets the total number of votes.
     *
     * @param $key
     *
     * @return Result|null
     */
    public function getCount($key)
    {
        $resultRepo = $this->em->getRepository('SymfonyContrib\Bundle\VotingBundle\Entity\Result');
        return $resultRepo->getResultByKey($key, 'count');
    }

    /**
     * Gets the average of votes.
     *
     * @param $key
     *
     * @return mixed
     */
    public function getAverage($key)
    {
        $resultRepo = $this->em->getRepository('SymfonyContrib\Bundle\VotingBundle\Entity\Result');
        return $resultRepo->getResultByKey($key, 'average');
    }

    /**
     * Add a result to the system.
     *
     * @param array $data
     * @param bool $flush
     */
    public function addResult(array $data, $flush = true)
    {
        $result = new Result($data);
        $this->em->persist($result);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Add several results at once.
     *
     * @param array $results
     */
    public function addResults(array $results)
    {
        foreach ($results as $result) {
            $this->addResult($result, false);
        }
        $this->em->flush();
    }

    /**
     * Calculate the sum, count, and average results.
     *
     * @param $key
     *
     * @return array
     */
    public function calculateResults($key)
    {
        $dql = "SELECT v.valueType, COUNT(v.value) AS vcount, SUM(v.value) AS vsum
                FROM VotingBundle:Vote v
                WHERE v.key = :key
                GROUP BY v.valueType";

        $rows = $this->em->createQuery($dql)
            ->setParameter('key', $key)
            ->getArrayResult();

        $results = [];
        foreach ($rows as $row) {
            $results[] = [
                'key' => $key,
                'value' => $row['vcount'],
                'valueType' => $row['valueType'],
                'method' => 'count',
            ];
            $results[] = [
                'key' => $key,
                'value' => $row['vsum'] / $row['vcount'],
                'valueType' => $row['valueType'],
                'method' => 'average',
            ];
            if ($row['valueType'] === 'points') {
                $results[] = [
                    'key' => $key,
                    'value' => $row['vsum'],
                    'valueType' => $row['valueType'],
                    'method' => 'sum',
                ];
            }
        }

        return $results;
    }

    /**
     * Update results for a specific key.
     *
     * @param $key
     */
    public function updateResults($key)
    {
        $this->removeResultsByKey($key);
        $results = $this->calculateResults($key);
        $this->addResults($results);
    }

}
