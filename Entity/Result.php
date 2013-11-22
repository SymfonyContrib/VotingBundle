<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\VotingBundle\Entity;

class Result {

    /**
     * @var string Unique identifier of a vote.
     */
    protected $id;

    /**
     * @var string Key for vote aggregation.
     */
    protected $key;

    /**
     * @var string Value of result.
     */
    protected $value;

    /**
     * @var string Type of result value.
     */
    protected $valueType;

    /**
     * @var string Method used to calculate the result.
     */
    protected $method;

    /**
     * @var int Time the result was calculated.
     */
    protected $timestamp;

    /**
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        $this->timestamp = $_SERVER['REQUEST_TIME'];

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
