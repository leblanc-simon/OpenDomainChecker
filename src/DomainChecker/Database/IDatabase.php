<?php

namespace DomainChecker\Database;

interface IDatabase
{
    public function connect();

    public function save();

    public function addServer($ip, $values);
    public function addDomain($domain, $values);

    public function findOne($type, $name);
    public function getServers();
    public function getDomains();
}