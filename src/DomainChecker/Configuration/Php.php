<?php

namespace DomainChecker\Configuration;

use DomainChecker\Filesystem\File;

class Php
    extends AConfiguration
    implements IConfiguration
{
    public function is()
    {
        try {
            $this->ssh->exec('[ -f /etc/php5/cgi/php.ini ]');
        } catch (\RuntimeException $e) {
            return false;
        }

        return true;
    }


    public function save()
    {
        $file = new File($this->directory, $this->ip, true);
        try {
            $this->ssh->exec('[ -f /etc/php5/cgi/php.ini ] && cat /etc/php5/cgi/php.ini');
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