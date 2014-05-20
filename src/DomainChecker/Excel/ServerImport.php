<?php

namespace DomainChecker\Excel;

use DomainChecker\Database\Database;

class ServerImport extends AImport
{	
	/**
	* Return Servers datas
	* Refer the sheetname
	*/
	public function getServersDatas()
	{
		$this->loadSheet('serveurs');
		$this->formatDatas();
		return $this->datas;
	}

	/**
	* Add and save servers in Database
	*
	*/
	public function saveServer($database)
	{
		$servers = $this->getServersDatas();
		foreach ($servers as $ip => $value) {
			$database->addServer($ip,$value);
		}
		$database->save();
	}
}
