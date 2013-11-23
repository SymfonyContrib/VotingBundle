<?php

namespace SymfonyContrib\Bundle\VotingBundle;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $anonValue;


    public function __construct(EntityManager $em, SecurityContextInterface $sc)
    {
        $this->em = $em;
        $this->sc = $sc;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setAnonValue($value = 'anon')
    {
        $this->anonValue = $value;
    }

    public function getAnonValue()
    {
        return $this->anonValue;
    }

    public function getVoteRepo()
    {
        return $this->em->getRepository('SymfonyContrib\Bundle\VotingBundle\Entity\Vote');
    }

    public function getResultRepo()
    {
        return $this->em->getRepository('SymfonyContrib\Bundle\VotingBundle\Entity\Result');
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

        // Return anonymous voter value.
        return $this->anonValue;
    }

    /**
     * Gets voter IP address.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->request->getClientIp();
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
        return $this->getVoteRepo()->getUserVote($key, $this->whoami());
    }

    /**
     * Adds a vote to the system.
     *
     * @param mixed $data
     * @param bool $updateResults
     *
     * @throws \Exception
     */
    public function addVote($data, $updateResults = true)
    {
        if (is_array($data)) {
            $vote = new Vote($data);
        } elseif ($data instanceof Vote) {
            $vote = $data;
        } else {
            throw new \Exception('Invalid argument. $data must be an array or Vote object.');
        }

        // Set defaults.
        $vote->setVoter($vote->getVoter() ?: $this->whoami());
        $vote->setIp($vote->getIp() ?: $this->getIp());

        // @todo Validate vote.

        // Save vote to the DB.
        $this->em->persist($vote);
        $this->em->flush($vote);

        // Update voting results.
        if ($updateResults) {
            $this->updateResults($vote->getKey());
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
        return $this->getResultRepo()->getResultByKey($key, 'sum');
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
        return $this->getResultRepo()->getResultByKey($key, 'count');
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
        return $this->getResultRepo()->getResultByKey($key, 'average');
    }

    /**
     * Add a result to the system.
     *
     * @param mixed $data
     * @param bool $flush
     *
     * @throws \Exception
     *
     * @return Result
     */
    public function addResult($data, $flush = true)
    {
        if (is_array($data)) {
            $result = new Result($data);
        } elseif ($data instanceof Result) {
            $result = $data;
        } else {
            throw new \Exception('Invalid argument. $data must be an array or Result object.');
        }

        $this->em->persist($result);

        // Allow disabling of auto flushing so inserts can be batched.
        if ($flush) {
            $this->em->flush($result);
        }

        return $result;
    }

    /**
     * Add several results at once.
     *
     * @param array $results
     */
    public function addResults(array $results)
    {
        foreach ($results as &$result) {
            $result = $this->addResult($result, false);
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
        // @todo: Make this pluggable to allow additional result methods.

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
        $this->getResultRepo()->removeResultsByKey($key);
        $results = $this->calculateResults($key);
        $this->addResults($results);
    }

    public function updateAllResults()
    {
        // Get all keys.



        foreach ($keys as $key) {
            $this->updateResults($key);
        }
    }

}
