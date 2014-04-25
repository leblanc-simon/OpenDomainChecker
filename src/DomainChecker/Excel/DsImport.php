<?php

namespace DomainChecker\Excel;

class DsImport extends AImport
{
    public function save($database)
    {
    	$fst_sheet = new DomainImport($this->file);
    	$fst_sheet->saveDomains($database);
    	
    	$snd_sheet = new ServerImport($this->file);
    	$snd_sheet->saveServer($database);
    	
    }
}