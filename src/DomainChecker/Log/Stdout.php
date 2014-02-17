<?php

namespace DomainChecker\Log;

use Symfony\Component\Console\Output\OutputInterface;

class Stdout
    extends ALog
    implements ILog
{
    public function writeLog($message, $type)
    {
        if (($this->output instanceof OutputInterface) === false) {
            echo '['.strtoupper($type).'] '.$message."\n";
            return;
        }

        $this->output->writeln('<'.$type.'>'.$message.'</'.$type.'>');
    }
}