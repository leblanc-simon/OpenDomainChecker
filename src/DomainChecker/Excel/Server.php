<?php

namespace DomainChecker\Excel;

class Server extends AExcel
{
    public function save($filename)
    {
        $this->createSheet('serveurs');
        $this->addDataInSheet($this->database->getServers());
        $this->write($filename);
    }
}