<?php

namespace DomainChecker\Command;

use DomainChecker\Domain\DomainCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Domain extends ACommand
{
    protected function configure()
    {
        $this
            ->setName('get:domain')
            ->setDescription('Get all available datas about domains');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->parseDomains();
        $this->processDomains();

        $this->database->save();
    }


    private function parseDomains()
    {
        $domain_collection = new DomainCollection($this->directory_ovh, $this->directory_apache);
        $domain_collection->setDatabaseProvider($this->database);
        $domain_collection->parse();
    }


    private function processDomains()
    {
        $domains = $this->database->getDomains();

        ksort($domains);

        foreach($domains as $domain_name => $infos) {
            $this->logComment('Process '.$domain_name);

            $domain = new \DomainChecker\Domain\Domain($domain_name);
            $domain->setDnsProvider($this->dns);
            $domain->initDnsInfos();

            $infos['dns_a'] = $domain->getA();
            $infos['dns_ns'] = $domain->getNs();
            $infos['dns_mx'] = $domain->getMx();
            $infos['primary'] = $domain->getPrimary();
            $infos['subname'] = $domain->getSubname();
            $infos['is_valid'] = $domain->getIsValid();
            $infos['is_primary'] = $domain->isPrimary();
            $infos['is_in_antispam'] = $domain->isInAntispam();
            $infos['has_multiple_mx'] = $domain->hasMultipleMx();
            $infos['is_hosted_by_us'] = $domain->isHostedByUs();
            $infos['is_mailed_by_us'] = $domain->isMailedByUs();
            $infos['has_bad_mailed_by_us'] = $domain->hasBadMailedByUs();

            $this->database->addDomain($domain_name, $infos);
        }
    }
}