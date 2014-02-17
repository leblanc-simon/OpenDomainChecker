<?php

namespace DomainChecker\Process;

use DomainChecker\Log\ILog;
use Symfony\Component\Process\ProcessBuilder;
use DomainChecker\Core\Config;

class Dns
{
    private $server = null;

    /**
     * @var ILog
     */
    private $logger = null;

    public function __construct($server = null)
    {
        if (null !== $server) {
            $this->setServer(domain);
        }
    }


    public function setServer($domain)
    {
        $this->server = $domain;

        return $this;
    }


    public function setLogger(ILog $logger)
    {
        $this->logger = $logger;
    }


    public function getNs($force_ip = null)
    {
        return $this->getDigDigDong('NS', $force_ip);
    }


    public function getA($force_ip = null)
    {
        return $this->_getA($force_ip);
    }


    private function _getA($server = null)
    {
        $ips = $this->getDigDigDong('A', $server);
        if (null === $ips) {
            return null;
        }

        // CNAME case : use only the last
        if (count($ips) > 1) {
            return $ips[count($ips) - 1];
        }

        return $ips[0];
    }


    public function getMx($force_ip = null)
    {
        $ips = $this->getDigDigDong('MX', $force_ip);
        if (null === $ips) {
            return null;
        }

        // Remove priorities
        $ips = array_map(function($value) {
            $values = explode(' ', $value);
            if (count($values) > 1) {
                return $values[1];
            }

            return $value;
        }, $ips);

        // MX is like CNAME, get the IP for each name
        $ips_tmp = array();
        foreach ($ips as $ip) {
            if (!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $ip)) {
                $ip_tmp = $this->_getA($ip);
                if (null !== $ip_tmp) {
                    $ip = $ip_tmp;
                }
            }
            $ips_tmp[] = $ip;
        }

        $ips = $ips_tmp;

        return $ips;
    }


    private function getDigDigDong($type, $force_ip = null)
    {
        $resolver = Config::get('resolver');
        if (is_array($resolver) === true) {
            $resolver = current($resolver);
        }

        $builder = new ProcessBuilder(array(
            'dig',
            '@'.$resolver,
            null === $force_ip ? $this->server : $force_ip,
            $type,
            '+short'
        ));

        $process = $builder->getProcess();
        $process->run();

        if ($process->isSuccessful() === false) {
            if (null !== $this->logger) {
                $this->logger->logError('Error while process the dig : '.$process->getCommandLine().' - '.$process->getErrorOutput());
            }
            return null;
        }

        $output = trim($process->getOutput());

        if (empty($output) === true) {
            if (null !== $this->logger) {
                $this->logger->logError('Empty result for the dig : '.$process->getCommandLine());
            }
            return null;
        }

        $output = explode("\n", $output);

        // Clean domain : delete final dot
        $output = array_map(function($value) {
            if (preg_match('/\.$/', $value)) {
                return substr($value, 0, -1);
            }

            return $value;
        }, $output);

        return $output;
    }
}