<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 11/01/14
 * Time: 18:00
 */

namespace Mrblackus\LaravelStoredprocedures;


class ModelStoredProcedure extends StoredProcedure implements ISPReturnClass
{
    /**
     * @return string
     */
    public function getCleanReturnType()
    {
        return $this->getReturnedClassName() . '[]';
    }

    /**
     * @return string
     */
    public function getReturnedClassName()
    {
        return Tools::capitalize(Tools::removeSFromTableName($this->returnType));
    }
}