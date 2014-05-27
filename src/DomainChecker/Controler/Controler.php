<?php

namespace DomainChecker\Controler;

use Silex\Application;
use Silex\Provider\UrlGeneratorServiceProvider;

class Controler
{
    /**
     * @var Application
     */
    private $application;

    public function __construct()
    {

    }


    public function init()
    {
        $this->application = new Application();

        $this->application->register(new UrlGeneratorServiceProvider());
        $this->application['debug']=1;
    }


    public function dispatch()
    {
        $routing = new Dispatcher();
        $routing->setApplication($this->application);
        $routing->initRouting();
    }


    public function run()
    {
        $this->application->run();
    }
}