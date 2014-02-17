<?php

namespace DomainChecker\Command;

use DomainChecker\Core\Config;
use DomainChecker\Database\Database;
use DomainChecker\Database\IDatabase;
use DomainChecker\Filesystem\Directory;
use DomainChecker\Log\ILog;
use DomainChecker\Log\File as FileLogger;
use DomainChecker\Log\Stdout;
use DomainChecker\Log\Multi;
use DomainChecker\Process\Ping;
use DomainChecker\Process\Hostname;
use DomainChecker\Process\Dns;
use DomainChecker\Ssh\Command as Ssh;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ACommand extends Command
{
    /**
     * @var Directory
     */
    protected $directory_apache = null;

    /**
     * @var Directory
     */
    protected $directory_openerp = null;

    /**
     * @var Directory
     */
    protected $directory_php = null;

    /**
     * @var Directory
     */
    protected $directory_ovh = null;

    /**
     * @var Directory
     */
    protected $directory_fis = null;

    /**
     * @var Ping
     */
    protected $ping = null;

    /**
     * @var Hostname
     */
    protected $hostname = null;

    /**
     * @var Ssh
     */
    protected $ssh = null;

    /**
     * @var Dns
     */
    protected $dns = null;

    /**
     * @var InputInterface
     */
    protected $input = null;

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var IDatabase
     */
    protected $database = null;

    /**
     * @var ILog
     */
    private $logger = null;


    public function __construct($name = null)
    {
        parent::__construct($name);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->setOutput($output);

        $this->init();
    }


    /**
     * Set the shell input
     *
     * @param InputInterface $input
     */
    protected function setInput(InputInterface $input)
    {
        $this->input = $input;
    }


    /**
     * Set the shell output
     *
     * @param OutputInterface $output
     */
    protected function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }


    /**
     * Output an info log
     *
     * @param   string  $message
     * @alias   self::logInfo
     */
    protected function log($message)
    {
        $this->logInfo($message);
    }

    /**
     * Output an info log
     *
     * @param   string  $message
     */
    protected function logInfo($message)
    {
        $this->writeLog($message, 'info');
    }

    /**
     * Output an error log
     *
     * @param   string  $message
     */
    protected function logError($message)
    {
        $this->writeLog($message, 'error');
    }

    /**
     * Output a comment log
     *
     * @param   string  $message
     */
    protected function logComment($message)
    {
        $this->writeLog($message, 'comment');
    }

    /**
     * Output the log message
     *
     * @param   string  $message
     * @param   string  $type
     */
    private function writeLog($message, $type = 'info')
    {
        $this->logger->writeLog($message, $type);
    }


    private function init()
    {
        $this->initLogger();

        $this->initDirectories();
        $this->initPing();
        $this->initSsh();
        $this->initDns();
        $this->initHostname();
        $this->initDatabase();
    }


    private function initDirectories()
    {
        $this->directory_apache     = new Directory(Config::get('data_dir').DIRECTORY_SEPARATOR.'apache');
        $this->directory_openerp    = new Directory(Config::get('data_dir').DIRECTORY_SEPARATOR.'openerp');
        $this->directory_php        = new Directory(Config::get('data_dir').DIRECTORY_SEPARATOR.'php');
        $this->directory_ovh        = new Directory(Config::get('data_dir').DIRECTORY_SEPARATOR.'ovh');
        $this->directory_fis        = new Directory(Config::get('data_dir').DIRECTORY_SEPARATOR.'fis');
    }


    private function initPing()
    {
        $this->ping = new Ping();
    }


    private function initSsh()
    {
        $this->ssh = new Ssh(
            Config::get('ssh_private_key'),
            array('-o', 'StrictHostKeyChecking=no', '-o', 'UserKnownHostsFile=/dev/null', '-q'),
            'root'
        );
    }


    private function initHostname()
    {
        if (($this->ssh instanceof Ssh) === false) {
            throw new \LogicException('ssh must be init before hostname');
        }

        $this->hostname = new Hostname($this->ssh);
    }


    private function initDns()
    {
        $this->dns = new Dns();
        $this->dns->setLogger($this->logger);
    }


    private function initDatabase()
    {
        $class_name = Config::get('database_class');
        $database_provider = new $class_name();

        if (($database_provider instanceof IDatabase) === false) {
            throw new \LogicException('database must be an instance of IDatabase');
        }

        $database = new Database($database_provider);

        $this->database = $database;
    }


    private function initLogger()
    {
        $this->logger = new Multi();

        $stdout_logger = new Stdout();
        $stdout_logger->setOutput($this->output);

        $file_logger = new FileLogger();
        $filename = strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
        $file_logger->setOutput(Config::get('log_dir').DIRECTORY_SEPARATOR.$filename.'.log');

        $this->logger->addLogger($stdout_logger);
        $this->logger->addLogger($file_logger);
    }
}