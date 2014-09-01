<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 15:34
 * To change this template use File | Settings | File Templates.
 */

namespace Mrblackus\LaravelStoredprocedures;

class Tools
{
    /**
     * @param string $str
     * @param int    $nbChars
     * @return string
     */
    public static function capitalize($str, $nbChars = 1)
    {
        for ($i = 0; $i < $nbChars; $i++)
        {
            $str[$i] = strtoupper($str[$i]);
        }
        return $str;
    }

    /**
     * @param string $str
     * @return string
     */
    public static function removeSFromTableName($str)
    {
        if (strtolower($str[strlen($str) - 1]) == 's')
            $str = substr($str, 0, -1);
        return $str;
    }
}