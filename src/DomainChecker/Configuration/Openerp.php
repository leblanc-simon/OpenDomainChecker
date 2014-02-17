<?php

namespace DomainChecker\Configuration;

use DomainChecker\Filesystem\File;

class Openerp
    extends AConfiguration
    implements IConfiguration
{
    public function is()
    {
        try {
            $this->ssh->exec('ps aux | grep openerp | grep -v grep > /dev/null');
        } catch (\RuntimeException $e) {
            return false;
        }

        return true;
    }


    public function save()
    {
        $file = new File($this->directory, $this->ip, true);
        try {
            $this->ssh->exec('[ -f /etc/init.d/openerpd ] && cat /etc/init.d/openerpd');
            $output = $this->ssh->getOutput();
            if (empty($output) === true) {
                return false;
            }

            $file->save($output);
        } catch (\RuntimeException $e) {
            return false;
        }

        return $this;
    }
}