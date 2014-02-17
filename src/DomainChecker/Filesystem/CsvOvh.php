<?php

namespace DomainChecker\Filesystem;

class CsvOvh extends Csv
{
    public $nichandle = null;
    public $type = null;
    public $reference = null;
    public $domain = null;
    public $expiration_date = null;
    public $nic_owner = null;
    public $nic_tech = null;
    public $nic_billing = null;
    public $nic_admin = null;
    public $nic_reseller = null;
    public $creation_date = null;
    public $reseller_profile = null;
    public $nic_owner_name = null;
    public $nic_owner_firstname = null;
    public $nic_owner_address = null;
    public $nic_owner_email = null;
    public $nic_owner_phone = null;
    public $dns = array();

    public function next()
    {
        $this->initVar();

        parent::next();
        
        if (false === $this->valid()) {
            $this->current_line = false;
            return;
        }

        $this->populate();
    }
    
    
    private function initVar()
    {
        $this->type = null;
        $this->reference = null;
        $this->domain = null;
        $this->expiration_date = null;
        $this->nic_owner = null;
        $this->nic_tech = null;
        $this->nic_billing = null;
        $this->nic_admin = null;
        $this->nic_reseller = null;
        $this->creation_date = null;
        $this->reseller_profile = null;
        $this->nic_owner_name = null;
        $this->nic_owner_firstname = null;
        $this->nic_owner_address = null;
        $this->nic_owner_email = null;
        $this->nic_owner_phone = null;
        $this->dns = array();
    }
    
    
    private function populate()
    {
        $vars = array(
            'type',
            'reference',
            'domain',
            'expiration_date',
            'nic_owner',
            'nic_tech',
            'nic_billing',
            'nic_admin',
            'nic_reseller',
            'creation_date',
            'reseller_profile',
            'nic_owner_name',
            'nic_owner_firstname',
            'nic_owner_address',
            'nic_owner_email',
            'nic_owner_phone',
        );

        foreach ($vars as $indice => $var) {
            if (isset($this->current_line[$indice]) === true) {
                $this->{$var} = $this->current_line[$indice];
            }
        }

        if (isset($this->current_line[17]) === true && 'Error in Dns resolution' !== $this->current_line[17]) {
            $dns = explode(',', $this->current_line[17]);
            $this->dns = array_map(function($v) { return trim($v); }, $dns);
        }

        $this->setNicHandle();

        $this->current_line = $this;
    }


    private function setNicHandle()
    {
        if (null === $this->nichandle) {
            $filename = basename($this->file->getRealPathname());

            if (preg_match('/^Services-([a-z0-9]+)-ovh/', $filename, $matches) === 1) {
                $this->nichandle = $matches[1].'-ovh';
            }
        }
    }
}