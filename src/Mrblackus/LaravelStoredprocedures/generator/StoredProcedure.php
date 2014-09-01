<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu.savy
 * Date: 21/08/13
 * Time: 10:21
 * To change this template use File | Settings | File Templates.
 */

namespace Mrblackus\LaravelStoredprocedures;

abstract class StoredProcedure
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $returnType;
    /** @var SPParameter[] */
    protected $parameters;

    /**
     * @param string        $name
     * @param string        $returnType
     * @param SPParameter[] $parameters
     */
    function __construct($name, $returnType, $parameters)
    {
        $this->name       = $name;
        $this->parameters = $parameters;
        $this->returnType = $returnType;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return Tools::capitalize($this->name, 4);
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
     * @param \Diapazon\Generator\SPParameter[] $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return \Diapazon\Generator\SPParameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $returnType
     */
    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;
    }

    /**
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @return string
     */
    public abstract function getCleanReturnType();

    /**
     * @return SPParameter[]
     */
    public function getINParameters()
    {
        $aINParameters = array();
        foreach ($this->parameters as $parameter)
        {
            if ($parameter->getMode() == SPParameter::PARAMETER_IN_MODE ||
                $parameter->getMode() == SPParameter::PARAMETER_INOUT_MODE
            )
            {
                $aINParameters[] = $parameter;
            }
        }

        usort($aINParameters, function ($paramA, $paramB)
        {
            /** @var $paramA SPParameter */
            /** @var $paramB SPParameter */
            if ($paramA->getPosition() == $paramB->getPosition())
                return 0;
            return $paramA->getPosition() > $paramB->getPosition() ? 1 : -1;
        });

        return $aINParameters;
    }

    /**
     * @return SPParameter[]
     */
    public function getOUParameters()
    {
        $aOUTParameters = array();
        foreach ($this->parameters as $parameter)
        {
            if ($parameter->getMode() == SPParameter::PARAMETER_OUT_MODE ||
                $parameter->getMode() == SPParameter::PARAMETER_INOUT_MODE
            )
            {
                $aOUTParameters[] = $parameter;
            }
        }

        usort($aOUTParameters, function ($paramA, $paramB)
        {
            /** @var $paramA SPParameter */
            /** @var $paramB SPParameter */
            if ($paramA->getPosition() == $paramB->getPosition())
                return 0;
            return $paramA->getPosition() > $paramB->getPosition() ? 1 : -1;
        });

        return $aOUTParameters;
    }
}