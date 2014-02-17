<?php

namespace DomainChecker\Database;

use DomainChecker\Core\Config;

class Json
    extends ADatabase
    implements IDatabase
{
    public function connect()
    {
        if (file_exists(Config::get('json_db')) === false) {
            return;
        }

        $this->init();
    }


    private function init()
    {
        if (file_exists(Config::get('json_db')) === false) {
            return false;
        }

        $content = file_get_contents(Config::get('json_db'));
        if (false === $content) {
            throw new \RuntimeException('Impossible to read json_db : '.Config::get('json_db'));
        }

        $json = json_decode($content, true);
        if (false === $json) {
            return false;
        }

        foreach ($this->available_types as $type) {
            if (isset($json[$type.'s']) === true) {
                $this->{$type.'s'} = $json[$type.'s'];
            }
        }
    }

    public function findOne($type, $name)
    {
        if (in_array($type, $this->available_types) === false) {
            throw new \InvalidArgumentException('type is not valid');
        }

        if (isset($this->{$type.'s'}[$name]) === true) {
            return $this->{$type.'s'}[$name];
        }

        return null;
    }

    public function save()
    {
        $json = array(
            'servers' => $this->servers,
            'domains' => $this->domains,
        );

        return (bool)file_put_contents(Config::get('json_db'), json_encode($json));
    }
}