<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\VotingBundle\Entity;

class Vote
{
    /**
     * Unique identifier of a vote.
     *
     * @var string
     */
    protected $id;

    /**
     * Key for vote aggregation.
     *
     * @var string
     */
    protected $key;

    /**
     * Value of vote.
     *
     * @var string
     */
    protected $value;

    /**
     * Type of vote value.
     *
     * @var string
     */
    protected $valueType;

    /**
     * Information representing the voter.
     *
     * @var string
     */
    protected $voter;

    /**
     * Time the vote was placed.
     *
     * @var int
     */
    protected $created;

    /**
     * @var string Agent/bundle who is storing the vote.
     */
    protected $agent;

    /**
     * @var string IP of user that cast the vote.
     */
    protected $ip;


    /**
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        $this->created = \DateTime::createFromFormat('U', $_SERVER['REQUEST_TIME']);

        if ($data !== null) {
            $this->setByArray($data);
        }
    }

    /**
     * Initialize object with an array of data.
     *
     * @param array $data
     */
    public function setByArray(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            $this->$method($value);
        }
    }

    /**
     * @param string $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $valueType
     */
    public function setValueType($valueType)
    {
        $this->valueType = $valueType;
    }

    /**
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }

    /**
     * @param string $voter
     */
    public function setVoter($voter)
    {
        $this->voter = $voter;
    }

    /**
     * @return string
     */
    public function getVoter()
    {
        return $this->voter;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

}
