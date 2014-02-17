<?php

namespace DomainChecker\Configuration;

use DomainChecker\Filesystem\File;

class Fis
    extends AConfiguration
    implements IConfiguration
{
    private $version = null;

    public function is()
    {
        try {
            $this->ssh->exec('[ -f /root/travaux/VERSION ] && cat /root/travaux/VERSION');
        } catch (\RuntimeException $e) {
            return false;
        }

        return true;
    }

    public function save()
    {
        $file = new File($this->directory, $this->ip, true);

        try {
            $this->ssh->exec('[ -f /root/travaux/VERSION ] && cat /root/travaux/VERSION');
            $this->version = $this->ssh->getOutput();
            $file->save($this->version);
        } catch (\RuntimeException $e) {
        }

        return $this;
    }


    public function getVersion()
    {
        return $this->version;
    }
}