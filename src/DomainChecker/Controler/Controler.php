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
