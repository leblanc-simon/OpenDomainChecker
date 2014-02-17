<?php

namespace DomainChecker\Core;

use DomainChecker\Ip\Ip;

class Config
{
    static private $datas = array();

    static public function add(array $datas)
    {
        foreach ($datas as $key => $value) {
            self::set($key, $value, true);
        }
    }


    static public function set($name, $value, $replace = false)
    {
        if (false === $replace && true === isset(self::$datas[$name])) {
            return false;
        }

        self::$datas[$name] = self::buildIp($value);
        return true;
    }


    static public function get($name, $default = null)
    {
        return true === isset(self::$datas[$name]) ? self::$datas[$name] : $default;
    }


    static private function buildIp($value)
    {
        $ip = new Ip();

        if (true === is_array($value)) {
            $values = array();
            foreach ($value as $key => $val) {
                $values = array_merge($values, self::buildIp($val));
            }

            return $values;
        }

        if (true === $ip->isIp($value)) {
            return $ip->setIp($value)->get();
        }

        return $value;
    }
}