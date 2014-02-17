<?php

namespace DomainChecker\Excel;

class DomainAndServer extends AExcel
{
    public function save($filename)
    {
        $this->createSheet('domaines');
        $this->addDataInSheet($this->database->getDomains());

        $this->createSheet('serveurs');
        $this->addDataInSheet($this->database->getServers());

        $this->write($filename);
    }
}