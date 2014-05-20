<?php

namespace DomainChecker\Controler;

use DomainChecker\Core\Config;
use DomainChecker\Excel\DsImport;
use DomainChecker\Excel\ServerImport;
use DomainChecker\Excel\DomainImport;
use Symfony\Component\HttpFoundation\Request;


class Import extends AControler 
{
    public function getServers()
    {
        $excel = new ServerImport('temp.xlsx');
        return $excel->getServersDatas();
    }

    public function saveServer()
    {
        $excel = new ServerImport('temp.xlsx');
    	$excel->saveServer($this->database);

        return $this->renderJson($this->database->getServers());
    }

    public function getDomains()
    {
        $excel = new DomainImport('temp.xlsx');
        return $excel->getDomainsDatas();
    }

    public function saveDomains()
    {
        $excel = new DomainImport('temp.xlsx');
        $excel->saveDomains($this->database);

        return $this->renderJson($this->database->getDomains());
    }
    /**
    * Save the 2 sheets
    */
    public function saveAll()
    {
        $excel = new DsImport('temp.xlsx');
        $excel->save($this->database);
    }

    public function receiveFile(Request $request)
    {
        $file = $request->files->get('upload');

        if (($file === null) || $file->getClientOriginalExtension()!=='xlsx') {
            $s = 'Aucun fichier ou mauvaise extension';

            header('Refresh: 3; URL= http://domaines.k.moulin.portailpro.net/');
            return $s ;
        }
        else
        {
            /* Move to data directory */
            $file -> move(Config::get('data_dir'),'temp.xlsx');
            $this->saveAll();
            $s = 'Importation reussie !';

            header('Refresh: 3; URL= http://domaines.k.moulin.portailpro.net/');
            return $s ;
        }
    }
}
