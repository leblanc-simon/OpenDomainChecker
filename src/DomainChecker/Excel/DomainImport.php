<?php

namespace DomainChecker\Excel;

use DomainChecker\Database\Database;

class DomainImport extends AImport
{	
	/**
	* Return Domains datas
	* Refer the sheetname
	*/
	public function getDomainsDatas()
	{
		$this->loadSheet('domaines');
		$this->formatDatas();
		return $this->datas;
	}
	
	/**
	* Add and save domains in Database
	*/
	public function saveDomains($database)
	{
		$domains = $this->getDomainsDatas();
		foreach ($domains as $domain => $value) {
			$database->addDomain($domain,$value);
		}
		$database->save();
	}

}
