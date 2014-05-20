<?php

namespace DomainChecker\Controler;
use DomainChecker\Core\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\ExceptionDataCollector;

class Putty extends AControler
{
    public function getFiles()
    {
        $filename = Config::get('data_dir').'/putty.zip';
        $generate=$this->database->generatePuttyFiles($filename);

        if (!file_exists($filename))
        {
            return new \Exception('Zip file doesn\'t exist');
        }

        if ($generate === true)
        {
        $content = file_get_contents($filename);

        $response = new Response();
        $response->setContent($content);
        $response->setStatusCode(200);
        $response->headers->set('Content-Disposition', 'attachment; filename="'.basename($filename).'"');
        $response->headers->set('Content-length',filesize($filename));

        unlink($filename);
        return $response->send();
        }
        else return new \Exception('Zip file doesn\'t exist');
    }
}
