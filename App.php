<?php

/**
 * Created by PhpStorm.
 * User: quynguyenlam
 * Date: 09.04.17
 * Time: 18:05
 */
class App
{
    protected static $registry = [];
    public static function bind($key,$value){
        static::$registry[$key] = $value;
    }

    public static function get($key){
        if(array_key_exists($key,self::$registry)){
            return static::$registry[$key];
        }else{
            throw new Exception("Es gibt keine dataBind für Key {$key}");
        }
    }

    public static function resolve($key){
        if(array_key_exists($key,self::$registry)){
            unset(static::$registry[$key]);
        }
    }
}