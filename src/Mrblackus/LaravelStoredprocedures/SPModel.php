<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 01/09/2014
 * Time: 15:06
 */

namespace Mrblackus\LaravelStoredprocedures;


abstract class SPModel
{
    /**
     * @param \PDOStatement $statement
     * @param string        $sParamName
     * @param mixed         $paramValue
     * @return bool
     */
    protected static function bindPDOValue(\PDOStatement &$statement, $sParamName, $paramValue)
    {
        if (is_bool($paramValue))
        {
            $iParamType = \PDO::PARAM_BOOL;
            $paramValue = $paramValue ? 'true' : 'false';
        }
        else if (is_int($paramValue))
            $iParamType = \PDO::PARAM_INT;
        else
            $iParamType = \PDO::PARAM_STR;

        return $statement->bindValue($sParamName, $paramValue, $iParamType);
    }

    /**
     * @param object  $model Model to hydrate
     * @param array   $data Associative array representing object values
     */
    protected static function hydrate(&$model, Array $data)
    {
        $r = new \ReflectionClass($model);
        $bEloquent = $r->isSubclassOf('Eloquent');
        foreach ($data as $k => $v)
        {
            if ($bEloquent)
            {
                $model->$k = $v;
            }
            else
            {
                $methodName = "set" . Tools::capitalize($k);
                if ($r->hasMethod($methodName))
                {
                    $method = $r->getMethod($methodName);
                    $method->setAccessible(true);
                    $method->invoke($model, $v);
                    $method->setAccessible(false);
                }
            }
        }
    }
} 