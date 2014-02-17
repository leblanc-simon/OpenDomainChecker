<?php

namespace DomainChecker\Database;

class Database
{
    private $database;

    public function __construct(IDatabase $database)
    {
        $this->database = $database;
        $this->database->connect();
    }


    public function getServers()
    {
        return $this->database->getServers();
    }


    public function getDomains()
    {
        return $this->database->getDomains();
    }


    public function addServer($ip, $values)
    {
        return $this->database->addServer($ip, $values);
    }


    public function addDomain($domain, $values)
    {
        return $this->database->addDomain($domain, $values);
    }


    public function save()
    {
        return $this->database->save();
    }
}