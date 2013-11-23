<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\VotingBundle\Entity;

class Result
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
     * Value of result.
     *
     * @var string
     */
    protected $value;

    /**
     * Type of result value.
     *
     * @var string
     */
    protected $valueType;

    /**
     * Method used to calculate the result.
     *
     * @var string
     */
    protected $method;

    /**
     * Time the result was calculated.
     *
     * @var int
     */
    protected $created;

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
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
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



}
