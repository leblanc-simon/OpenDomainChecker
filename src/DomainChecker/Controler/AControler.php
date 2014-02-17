<?php

namespace DomainChecker\Controler;

use DomainChecker\Core\Config;
use DomainChecker\Database\Database;
use Silex\Application;

abstract class AControler
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Database
     */
    protected $database;

    public function __construct(Application $application)
    {
        $this->application = $application;

        $database_class = Config::get('database_class');
        $this->database = new Database(new $database_class());
    }


    protected function renderError($message)
    {
        $this->application->json(array('message' => $message), 500);
    }


    protected function renderJson($datas)
    {
        return $this->application->json($datas);
    }
}