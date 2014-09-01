<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 14/01/2014
 * Time: 11:18
 */

namespace Mrblackus\LaravelStoredprocedures;

interface ISPReturnClass
{
    /**
     * @return string
     */
    public function getReturnedClassName();
} 