<?php

namespace DomainChecker\Excel;

use DomainChecker\Database\Database;
use DomainChecker\Core\Config;

abstract class AImport
{
    /**
     * @var excel loaded from the file 
     */
    protected $excel;

    /**
     * @var filename
     */
    protected $file;

	 /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var datas from excel
     */
    protected $datas;

    public function __construct($file)
    {
        $this->file = $file;
        
    }

    public function getFilename()
    {
      return self::$file;
    }

	/**
	* Load the sheet from data_dir and convert to an array 
	*/
    public function loadSheet($sheetname)
    {
    	$this->reader = new \PHPExcel_Reader_Excel2007();
        $this->reader->setLoadSheetsOnly($sheetname);
        $this->excel = $this->reader->load(Config::get('data_dir').'/'.self::$this->file);
    }

    /**
    * @return multi dim array 
    * Extract and convert data from excel
    * 
    */
    public function formatDatas()
    {
    	$this->datas = $this->excel->getActiveSheet()->toArray(null,true,true,true);

        $config_fields = explode(',', Config::get('additionnal_fields'));
        $validated = array();

        foreach ($this->datas[1] as $column => $field) {
            if (in_array($field,$config_fields)) {
                $validated[$column] = $field;
            }
        }

        $return_table = array();
        foreach ($this->datas as $value) {    
            $temp = array();
            foreach ($validated as $col_name => $col_value) {
                /* Not null values */
                if ($value[$col_name]) {
                    $temp[$col_value] = $value[$col_name];
                    $return_table[$value['A']] = array();
                    $return_table[$value['A']] = $temp;
                }
            }   
        }

        if (isset($return_table['ip'])) {
            unset($return_table['ip']);
        }
        elseif (isset($return_table['domain'])) {
            unset($return_table['domain']);
        }

        $this->datas = $return_table;
    }

    public function getDatas()
    {
        return $this->datas;
    }
}
