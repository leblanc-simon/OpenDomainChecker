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
        $serv = 'http://'.$request->getHttpHost();
        $message = 'Aucun fichier ou mauvaise extension';
        if (($file === null) || $file->getClientOriginalExtension()!=='xlsx') {
            header('Refresh: 2; URL='.$serv);
            return $message ;
        }
        else
        {
            /* Move to data directory */
            $file -> move(Config::get('data_dir'),'temp.xlsx');
            $this->saveAll();
            $message = 'Importation reussie !';
            header('Refresh: 2; URL='.$serv);
            return $message ;
        }
    }
}
