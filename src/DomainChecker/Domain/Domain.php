<?php

namespace DomainChecker\Domain;

use DomainChecker\Core\Config;
use DomainChecker\Log\ILog;
use DomainChecker\Process\Dns;

class Domain
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string  the IP of A field
     */
    private $a = null;

    /**
     * @var array   the list of IP of the MX field
     */
    private $mx = array();

    /**
     * @var array   the list of IP of the NS field
     */
    private $ns = array();

    /**
     * @var bool
     */
    private $is_primary = null;

    /**
     * @var bool
     */
    private $is_valid_hostname = null;

    /**
     * @var string  the top level of the domain name
     */
    private $primary_name = null;

    /**
     * @var string  the subdomain
     */
    private $sub_name = null;

    /**
     * @var Dns
     */
    private $dns_provider = null;

    /**
     * @var ILog
     */
    private $logger = null;


    public function __construct($name)
    {
        if (is_string($name) === false) {
            throw new \RuntimeException('name must be a string');
        }

        $this->name = trim($name);

        $this->checkName();
    }


    public function setDnsProvider(Dns $dns_provider)
    {
        $this->dns_provider = $dns_provider;
        $this->dns_provider->setServer($this->name);
    }


    public function setLogger(ILog $logger)
    {
        $this->logger = $logger;
    }


    public function initDnsInfos()
    {
        $this->a    = $this->getDnsProvider()->getA();

        if (true === $this->is_primary) {
            $this->mx   = $this->getDnsProvider()->getMx();
            $this->ns   = $this->getDnsProvider()->getNs();
        } else {
            $this->mx   = $this->getDnsProvider()->getMx($this->primary_name);
            $this->ns   = $this->getDnsProvider()->getNs($this->primary_name);
        }
    }


    public function getA()
    {
        return $this->a;
    }

    public function getMx()
    {
        return $this->mx;
    }

    public function getNs()
    {
        return $this->ns;
    }

    public function getPrimary()
    {
        return $this->primary_name;
    }

    public function getSubname()
    {
        return $this->sub_name;
    }

    public function getIsValid()
    {
        return $this->is_valid_hostname;
    }

    public function isPrimary()
    {
        return $this->is_primary;
    }

    public function isInAntispam()
    {
        if (null === $this->mx || (is_array($this->mx) === true && count($this->mx) === 0)) {
            return false;
        }

        $antispam_ips = Config::get('spamserver_ips');

        foreach ($this->mx as $ip) {
            if (in_array($ip, $antispam_ips) === true) {
                return true;
            }
        }

        return false;
    }


    public function hasMultipleMx()
    {
        if (is_array($this->mx) === true && count($this->mx) > 1) {
            return true;
        }

        return false;
    }


    public function isHostedByUs()
    {
        return in_array($this->a, Config::get('webserver_ips'));
    }


    public function isMailedByUs()
    {
        if (is_array($this->mx) === false || count($this->mx) === 0) {
            return false;
        }

        foreach ($this->mx as $mx) {
            if (in_array($mx, array_merge(Config::get('webserver_ips'), Config::get('mxserver_ips'), Config::get('spamserver_ips'))) === true) {
                return true;
            }
        }

        return false;
    }


    public function hasBadMailedByUs()
    {
        if (false === $this->isMailedByUs()) {
            return false;
        }

        foreach ($this->mx as $mx) {
            if (in_array($mx, Config::get('webserver_ips'))
                &&
                in_array($mx, array_merge(Config::get('mxserver_ips'), Config::get('spamserver_ips'))) === false
            ) {
                return true;
            }
        }

        return false;
    }


    public function isDnsInOurServer()
    {
        // TODO : implements this
    }


    /**
     * Return the DNS provider
     *
     * @return Dns                  The DNS provider
     * @throws \RuntimeException    If the DNS provider is not defined
     */
    private function getDnsProvider()
    {
        if (null === $this->dns_provider) {
            throw new \RuntimeException('dns_provider must be defined');
        }

        return $this->dns_provider;
    }


    /**
     * Check the validity of the domain and verify if it's a primary domain or subdomain
     *
     * @return  bool    true if it's a valid domain, false else (local or wildcard by example)
     */
    private function checkName()
    {
        if (substr_count($this->name, '.') === 1) {
            $this->is_valid_hostname = true;
            $this->is_primary = true;
            $this->primary_name = $this->name;
            return true;
        } elseif (substr_count($this->name, '.') === 0) {
            // WTF !!! local domain name
            $this->is_valid_hostname = false;
            return false;
        }

        $this->is_primary = false;
        $position = strrpos($this->name, '.', -1 * (strlen($this->name) - strrpos($this->name, '.') + 1));
        $this->primary_name = substr($this->name, $position + 1);
        $this->sub_name = substr($this->name, 0, $position);

        $this->is_valid_hostname = (substr($this->sub_name, 0, 1) !== '*');

        return $this->is_valid_hostname;
    }
}