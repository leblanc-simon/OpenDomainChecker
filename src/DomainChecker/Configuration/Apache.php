<?php

namespace DomainChecker\Configuration;

use DomainChecker\Filesystem\File;

class Apache
    extends AConfiguration
    implements IConfiguration
{
    public function is()
    {
        try {
            $this->ssh->exec('ps aux | grep -E "(apache|httpd)" | grep -v grep > /dev/null');
        } catch (\RuntimeException $e) {
            return false;
        }

        return true;
    }


    public function save()
    {
        $file = new File($this->directory, $this->ip, true);

        $this->saveOld($file);
        $this->saveNew($file);

        return $this;
    }


    private function saveOld(File $file)
    {
        try {
            $this->ssh->exec('[ -d /etc/apache2/sites-enabled ] && cat /etc/apache2/sites-enabled/* | grep -iE "(ServerName|ServerAlias)" | sed "s/^ *//;s/ *$//" | grep -vE "^#" | awk "{print \\$2}"');
            $output = $this->ssh->getOutput();
            if (empty($output) === true) {
                return false;
            }

            $file->save($output);
        } catch (\RuntimeException $e) {
            return false;
        }

        return true;
    }


    private function saveNew(File $file)
    {
        try {
            $this->ssh->exec('[ -f /httpd.conf ] && cat /httpd.conf | grep -iE "(ServerName|ServerAlias)" | sed "s/^ *//;s/ *$//" | grep -vE "^#" | awk "{print \\$2}"');
            $output = $this->ssh->getOutput();
            if (empty($output) === true) {
                return false;
            }

            $file->save($output);
        } catch (\RuntimeException $e) {
            return false;
        }

        return true;
    }
}