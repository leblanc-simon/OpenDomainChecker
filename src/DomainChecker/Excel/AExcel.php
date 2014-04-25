<?php

namespace DomainChecker\Excel;

use DomainChecker\Database\Database;

abstract class AExcel
{
    /**
     * @var Database
     */
    protected $database;

    /**
     * @var \PHPExcel
     */
    private $excel = null;

    /**
     * @var int
     */
    private $sheet = 0;

    /**
     * @var array
     */
    private $titles = array();


    public function __construct(Database $database)
    {
        $this->database = $database;
    }


    protected function createSheet($type)
    {
        if (0 !== $this->sheet) { // if 0, the sheet already exist
            $this->getExcel()->createSheet($this->sheet);
        }
        $this->getExcel()->setActiveSheetIndex($this->sheet);
        $this->getExcel()->getActiveSheet()->setTitle($type);
        $this->getExcel()->getActiveSheet()->freezePane('B2');

        $this->sheet++;

        return $this;
    }


    protected function addDataInSheet($datas)
    {
        $this->getTitles($datas);

        $line = 1; // Yes Excel begin to 1...
        foreach ($this->titles as $title => $position) {
            $this->getExcel()->getActiveSheet()->setCellValue($position.$line, $title);
        }

        $line++;
        foreach ($datas as $data) {
            foreach ($data as $key => $value) {
                $this->getExcel()->getActiveSheet()->setCellValue($this->titles[$key].$line, $this->convertValue($value));
            }
            $line++;
        }
    }


    protected function write($filename)
    {
        $writer = new \PHPExcel_Writer_Excel2007($this->getExcel());
        $writer->save($filename);
    }

    private function getExcel()
    {
        if (null === $this->excel) {
            $this->excel = new \PHPExcel();

            $this->excel->getProperties()->setCreator('OpenDomainChecker');
            $this->excel->getProperties()->setLastModifiedBy('OpenDomainChecker');
            $this->excel->getProperties()->setTitle('OpenDomainChecker');
        }

        return $this->excel;
    }


    private function getTitles($datas)
    {
        $this->titles = array();
        $positions = $this->getRangeLetter();
        $position = 0;

        foreach ($datas as $data) {
            foreach (array_keys($data) as $title) {
                if (array_key_exists($title, $this->titles) === false) {
                    $this->titles[$title] = $positions[$position++];
                }
            }
        }
    }


    private function getRangeLetter()
    {
        $letters = range('A', 'Z');
        $letters2 = range('A', 'Z');
        $columns = $letters;

        // Iterate over 26 letters.
        foreach ($letters as $letter) {
            foreach ($letters2 as $letter2) {
                $columns[] = $letter.$letter2;
            }
        }

        return $columns;
    }


    private function convertValue($value)
    {
        if (is_array($value) === true) {
            return implode(', ', $value);
        }

        if (is_bool($value) === true) {
            return $value ? 'Oui' : 'Non';
        }

        if (null === $value) {
            return '';
        }

        return $value;
    }
}

