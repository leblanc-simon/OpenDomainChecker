<?php

namespace DomainChecker\Domain;

use DomainChecker\Database\Database;
use DomainChecker\Filesystem\CsvOvh;
use DomainChecker\Filesystem\Directory;

class DomainCollection
{
    /**
     * @var Directory
     */
    private $directory_ovh;

    /**
     * @var Directory
     */
    private $directory_apache;

    /**
     * @var Database
     */
    private $database = null;

    public function __construct(Directory $directory_ovh, Directory $directory_apache)
    {
        $this->directory_ovh = $directory_ovh;
        $this->directory_apache = $directory_apache;
    }


    public function setDatabaseProvider(Database $database)
    {
        $this->database = $database;
    }


    public function parse()
    {
        $this->parseOvh();
        $this->parseApache();
    }


    private function parseOvh()
    {
        $files = $this->directory_ovh->getAll();

        foreach ($files as $file) {
            $csv = new CsvOvh($file);

            foreach ($csv as $line) {
                if ('DOMAIN' !== $csv->type) {
                    continue;
                }

                $domain = array(
                    'domain' => $csv->domain,
                    'nichandle' => $csv->nichandle,
                    'expiration_date' => $csv->expiration_date,
                    'nic_owner' => $csv->nic_owner,
                    'nic_tech' => $csv->nic_tech,
                    'nic_billing' => $csv->nic_billing,
                    'nic_admin' => $csv->nic_admin,
                    'nic_reseller' => $csv->nic_reseller,
                    'creation_date' => $csv->creation_date,
                    'reseller_profile' => $csv->reseller_profile,
                    'nic_owner_name' => $csv->nic_owner_name,
                    'nic_owner_firstname' => $csv->nic_owner_firstname,
                    'nic_owner_address' => $csv->nic_owner_address,
                    'nic_owner_email' => $csv->nic_owner_email,
                    'nic_owner_phone' => $csv->nic_owner_phone,
                    'found_in_ovh' =>  $csv->nichandle,
                );

                $this->database->addDomain($csv->domain, $domain);
            }
        }
    }


    private function parseApache()
    {
        $files = $this->directory_apache->getAll();

        foreach ($files as $file) {
            $content = $file->getContent();
            $domains = explode("\n", $content);

            foreach ($domains as $domain) {
                if (empty($domain) === true) {
                    continue;
                }

                $this->database->addDomain($domain, array(
                    'domain' => $domain,
                    'found_in_server' => pathinfo($file->getRealPathname(), PATHINFO_FILENAME),
                ));
            }
        }
    }
}