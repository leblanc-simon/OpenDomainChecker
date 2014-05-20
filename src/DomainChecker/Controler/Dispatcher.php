<?php

namespace DomainChecker\Controler;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Dispatcher
{
    /**
     * @var Application
     */
    private $application;

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }


    public function initRouting()
    {
        $this->setHomepage();

        $this->setServers();
        $this->setDomains();
        $this->setExport();
        $this->setEdit();

        $this->setImport();
        $this->setPutty();
    }


    private function setHomepage()
    {
        $self = $this;

        $this->application->get('/', function() use ($self) {
            $controler = new Homepage($self->application);
            return $controler->get();
        })->bind('homepage');
    }


    private function setServers()
    {
        $self = $this;

        $this->application->get('/servers', function() use ($self) {
            $controler = new Servers($self->application);
            return $controler->get();
        })->bind('servers');

        $this->application->get('/servers/{ip}', function($ip) use ($self) {
            $controler = new Servers($self->application);
            return $controler->show($ip);
        })->bind('server');
    }


    private function setDomains()
    {
        $self = $this;

        $this->application->get('/domains', function() use ($self) {
            $controler = new Domains($self->application);
            return $controler->get();
        })->bind('domains');

        $this->application->get('/domains/{name}', function($name) use ($self) {
            $controler = new Domains($self->application);
            return $controler->show($name);
        })->bind('domain');
    }


    private function setExport()
    {
        $self = $this;

        $this->application->get('/export/all', function() use ($self) {
            $controler = new Export($self->application);
            return $controler->getAll();
        })->bind('export-all');

        $this->application->get('/export/servers', function() use ($self) {
            $controler = new Export($self->application);
            return $controler->getServers();
        })->bind(('export-servers'));

        $this->application->get('/export/domains', function() use ($self) {
            $controler = new Export($self->application);
            return $controler->getDomains();
        })->bind('export-domains');
    }


    private function setEdit()
    {
        $self = $this;

        $this->application->post('/edit', function(Request $request) use ($self) {
            $controler = new Edit($self->application);
            return $controler->save($request);
        })->bind('edit');
    }

    private function setImport()
    {
        $self = $this;

        $this->application->post('/import', function(Request $request) use ($self) {
            $controler = new Import($self->application);
            return $controler->receiveFile($request);
        })->bind('import');

    }

    private function setPutty()
    {
        $self = $this;

        $this->application->get('/putty', function() use ($self) {
            $controler = new Putty($self->application);
            return $controler->getFiles();
        })->bind('putty');
    }

}

