<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 11/01/14
 * Time: 17:46
 */

namespace Mrblackus\LaravelStoredprocedures;

class SPParameter
{
    const PARAMETER_IN_MODE    = 'IN';
    const PARAMETER_OUT_MODE   = 'OUT';
    const PARAMETER_INOUT_MODE = 'INOUT';

    /** @var string */
    private $name;
    /** @var int */
    private $position;
    /** @var string */
    private $type;
    /** @var int */
    private $mode;

    public function __construct($name, $type, $mode, $position)
    {
        $this->name     = trim($name);
        $this->type     = trim($type);
        $this->position = intval($position);

        switch ($mode)
        {
            case 'OUT':
                $this->mode = self::PARAMETER_OUT_MODE;
                break;
            case 'INOUT':
                $this->mode = self::PARAMETER_INOUT_MODE;
                break;
            default:
                $this->mode = self::PARAMETER_IN_MODE;
                break;
        }
    }

    /**
     * @return bool
     */
    public function isStringType()
    {
        return $this->isTypeString($this->type);
    }

    /**
     * @param string $str
     * @return bool
     */
    private function isTypeString($str)
    {
        $stringType = array(
            'character varying',
            'character',
            'text'
        );

        return in_array($str, $stringType);
    }

    /**
     * @param int $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
} 