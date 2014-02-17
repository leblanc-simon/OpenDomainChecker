<?php

namespace DomainChecker\Command;

use DomainChecker\Core\Config;
use DomainChecker\Configuration\Apache;
use DomainChecker\Configuration\Fis;
use DomainChecker\Configuration\Openerp;
use DomainChecker\Configuration\Php;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Server extends ACommand
{
    /**
     * @var array
     */
    private $server = null;

    protected function configure()
    {
        $this
            ->setName('get:server')
            ->setDescription('Get all available datas about servers');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->clearDirectories();

        foreach (Config::get('webserver_ips') as $ip) {
            $this->processServer($ip);
            $this->database->addServer($ip, $this->server);
        }

        $this->database->save();
    }


    /**
     * Remove all data store in the configuration directories
     */
    private function clearDirectories()
    {
        $this->logComment('Begin cleaning directory');

        $this->directory_apache->clear();
        $this->directory_openerp->clear();
        $this->directory_php->clear();
        $this->directory_fis->clear();

        $this->logComment('End cleaning directory');
    }


    /**
     * Check the server and get all informations
     *
     * @param   string  $ip The IP of the server to check
     */
    private function processServer($ip)
    {
        $this->logComment('Begin processing '.$ip);

        $this->initServer($ip);

        // Check ping
        $ping = $this->checkPing($ip);
        $this->setPing($ping);

        if (false === $ping) {
            $this->logComment('Premature end of processing '.$ip);
            return;
        }

        if (in_array($ip, Config::get('exclude_ssh_ips')) === true) {
            $this->logComment('Premature end of processing, no SSH access for '.$ip);
            return;
        }

        // Get the server hostname
        $this->setHostname($ip);

        // Get Apache informations
        $this->setApache($ip);

        // Get OpenERP informations
        $this->setOpenerp($ip);

        // Get PHP informations
        $this->setPhp($ip);

        // Get FreeInstallServer informations
        $this->setFis($ip);

        $this->logComment('End processing '.$ip);
    }


    /**
     * Init the data for the current server checked
     *
     * @param   string  $ip The IP of the current server checked
     */
    private function initServer($ip)
    {
        $this->server = array(
            'ip'        => $ip,
            'ping'      => null,
            'hostname'  => null,
            'apache'    => null,
            'openerp'   => null,
            'php'       => null,
            'fis'       => null,
        );
    }


    /**
     * Set the state of ping for the current server checked
     *
     * @param   bool    $ping   the state of ping
     */
    private function setPing($ping)
    {
        $this->server['ping'] = (bool)$ping;
    }


    /**
     * Check if the server ping
     *
     * @param   string  $ip the ip of the server
     * @return  bool        true if the server ping, false else
     */
    private function checkPing($ip)
    {
        $this->logComment('check ping for '.$ip);

        $ping = $this->ping->setServer($ip)->check();
        if (false === $ping) {
            $this->logError($ip.' : no ping');
            return false;
        }

        $this->logInfo($ip.' : ping');
        return true;
    }


    /**
     * Retrieve the hostname of the server
     *
     * @param   string  $ip the IP of the server
     * @return  bool        true if the hostname is found, false else
     */
    private function setHostname($ip)
    {
        $this->logComment('get hostname for '.$ip);

        $hostname = $this->hostname->setServer($ip)->getHostname();
        if (null === $hostname) {
            $this->server['hostname'] = false;
            $this->logError($ip.' : no hostname');
            return false;
        }

        $this->server['hostname'] = $hostname;
        $this->logInfo('hostname for '.$ip.' : '.$hostname);
        return true;
    }


    /**
     * Retrieve the apache configuration of the server
     *
     * @param   string  $ip the IP of the server
     * @return  bool        true if the server has apache server, false else
     */
    private function setApache($ip)
    {
        $this->logComment('get apache for '.$ip);

        $apache = new Apache($this->ssh, $ip, $this->directory_apache);
        $is = $apache->is();
        if (false === $is) {
            $this->server['apache'] = false;
            $this->logInfo($ip.' : no apache');
            return false;
        }

        $apache->save();
        $this->server['apache'] = true;
        $this->logInfo($ip.' : apache');
    }


    /**
     * Retrieve the OpenERP configuration of the server
     *
     * @param   string  $ip the IP of the server
     * @return  bool        true if the server has OpenERP server, false else
     */
    private function setOpenerp($ip)
    {
        $this->logComment('get openerp for '.$ip);

        $openerp = new Openerp($this->ssh, $ip, $this->directory_openerp);
        $is = $openerp->is();
        if (false === $is) {
            $this->server['openerp'] = false;
            $this->logInfo($ip.' : no openerp');
            return false;
        }

        $openerp->save();
        $this->server['openerp'] = true;
        $this->logInfo($ip.' : openerp');
    }


    /**
     * Retrieve the PHP configuration of the server
     *
     * @param   string  $ip the IP of the server
     * @return  bool        true if the server has PHP configuration, false else
     */
    private function setPhp($ip)
    {
        $this->logComment('get php for '.$ip);

        $php = new Php($this->ssh, $ip, $this->directory_php);
        $is = $php->is();
        if (false === $is) {
            $this->server['php'] = false;
            $this->logInfo($ip.' : no php');
            return false;
        }

        $php->save();
        $this->server['php'] = true;
        $this->logInfo($ip.' : php');
    }


    /**
     * Retrieve the FreeInstallServer configuration of the server
     *
     * @param   string  $ip the IP of the server
     * @return  bool        true if the server has FreeInstallServer configuration, false else
     */
    private function setFis($ip)
    {
        $this->logComment('get FreeInstallServer for '.$ip);

        $fis = new Fis($this->ssh, $ip, $this->directory_fis);
        $is = $fis->is();
        if (false === $is) {
            $this->server['fis'] = false;
            $this->logError($ip.' : no FreeInstallServer');
            return false;
        }

        $fis->save();
        $this->server['fis'] = $fis->getVersion();
        $this->logInfo($ip.' : FreeInstallServer');
    }
}