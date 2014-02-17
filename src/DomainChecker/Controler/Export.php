<?php

namespace DomainChecker\Controler;

use DomainChecker\Core\Config;
use DomainChecker\Excel\DomainAndServer;
use DomainChecker\Excel\Server as ExcelServer;
use DomainChecker\Excel\Domain as ExcelDomain;

class Export extends AControler
{
    public function getAll()
    {
        $filename = Config::get('data_dir').'/domains_and_servers.xlsx';

        $excel = new DomainAndServer($this->database);
        $excel->save($filename);

        return $this->application->sendFile($filename, 200, array(
            'Content-Disposition' => 'attachment; filename="'.basename($filename).'"',
        ));
    }


    public function getServers()
    {
        $filename = Config::get('data_dir').'/servers.xlsx';

        $excel = new ExcelServer($this->database);
        $excel->save($filename);

        return $this->application->sendFile($filename, 200, array(
            'Content-Disposition' => 'attachment; filename="'.basename($filename).'"',
        ));
    }


    public function getDomains()
    {
        $filename = Config::get('data_dir').'/domains.xlsx';

        $excel = new ExcelDomain($this->database);
        $excel->save($filename);

        return $this->application->sendFile($filename, 200, array(
            'Content-Disposition' => 'attachment; filename="'.basename($filename).'"',
        ));
    }
}