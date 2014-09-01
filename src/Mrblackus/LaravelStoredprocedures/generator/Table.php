<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 02/06/2014
 * Time: 17:01
 */

namespace Mrblackus\LaravelStoredprocedures;

class Table
{
    private $name;

    public function __construct($sName)
    {
        $this->name = $sName;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
} 