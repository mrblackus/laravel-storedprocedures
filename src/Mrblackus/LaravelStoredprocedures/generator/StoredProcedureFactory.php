<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 11/01/14
 * Time: 17:38
 */

namespace Mrblackus\LaravelStoredprocedures;

class StoredProcedureFactory
{
    /** @var array */
    private $tableNames;

    /**
     * @param Table[] $tables
     */
    public function __construct($tables)
    {
        foreach ($tables as $t)
            $this->tableNames[] = $t->getName();
    }

    /**
     * @param string        $name
     * @param string        $returnType
     * @param SPParameter[] $parameters
     * @return StoredProcedure
     */
    public function getStoredProcedure($name, $returnType, $parameters = array())
    {
        //Model stored procedure
        if (in_array($returnType, $this->tableNames))
            $storeProcedure = new ModelStoredProcedure($name, $returnType, $parameters);
        else if ($returnType == 'record')
            $storeProcedure = new RecordStoredProcedure($name, $returnType, $parameters);
        else
            $storeProcedure = new ScalarStoredProcedure($name, $returnType, $parameters);

        return $storeProcedure;
    }
}