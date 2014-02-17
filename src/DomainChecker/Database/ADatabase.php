<?php

namespace DomainChecker\Database;

abstract class ADatabase
{
    /**
     * @var array
     */
    protected $available_types = array('server', 'domain');

    /**
     * @var array
     */
    protected $servers = array();

    /**
     * @var array
     */
    protected $domains = array();


    /**
     * Add a server to save
     *
     * @param   string  $ip     the IP address of the server
     * @param   array   $values the values to save (key => value)
     * @return  $this
     */
    public function addServer($ip, $values)
    {
        if (isset($this->servers[$ip]) === true) {
            $values = array_merge($this->servers[$ip], $values);
        }

        $this->servers[$ip] = $values;
        ksort($this->servers);

        return $this;
    }


    /**
     * Add a domain name to save
     *
     * @param   string  $domain the domain name
     * @param   array   $values the values to save (key => value)
     * @return  $this
     */
    public function addDomain($domain, $values)
    {
        if (isset($this->domains[$domain]) === true) {
            $values = array_merge($this->domains[$domain], $values);
        }

        $this->domains[$domain] = $values;
        ksort($this->domains);

        return $this;
    }


    /**
     * Return all servers
     *
     * @return  array   an array with all servers
     */
    public function getServers()
    {
        return $this->servers;
    }


    /**
     * Return all domains
     *
     * @return  array   an array with all domains
     */
    public function getDomains()
    {
        return $this->domains;
    }
}