<?php

namespace DomainChecker\Excel;

class Domain extends AExcel
{
    public function save($filename)
    {
        $this->createSheet('domaines');
        $this->addDataInSheet($this->database->getDomains());
        $this->write($filename);
    }
}