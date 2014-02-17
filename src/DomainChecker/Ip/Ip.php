<?php

namespace DomainChecker\Ip;

class Ip
{
    private $internal_ips = array();
    private $is_range = false;
    private $range = null;
    private $start = null;

    public function __construct($ip = null)
    {
        if ($ip !== null) {
            $this->setIp($ip);
        }
    }


    public function get()
    {
        return $this->internal_ips;
    }


    private function init()
    {
        $this->internal_ips = array();
        $this->is_range     = false;
        $this->range        = null;
        $this->start        = null;
    }


    public function setIp($ip)
    {
        $this->init();

        if (preg_match('#([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})(/([0-9]+))?#', $ip, $match) == 0) {
            throw new \InvalidArgumentException('Bad format for the IP');
        }

        $this->start = $match[1];

        if (empty($match[3]) === true) {
            $this->internal_ips[] = $match[1];
        } else {
            $this->buildRange($match[1], $match[3]);
        }

        return $this;
    }


    public function isIp($ip)
    {
        return (bool)preg_match('#([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})(/([0-9]+))?#', $ip);
    }


    private function buildRange($ip, $range)
    {
        $start = ip2long($ip);
        $ip_count = 1 << (32 - $range);

        $this->range = $range;
        $this->is_range = true;

        for ($iterator = 0; $iterator < $ip_count; $iterator++) {
            $this->internal_ips[] = long2ip($start + $iterator);
        }
    }
}