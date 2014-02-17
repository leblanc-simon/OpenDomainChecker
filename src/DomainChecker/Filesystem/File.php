<?php

namespace DomainChecker\Filesystem;

class File
{
    private $directory = null;
    private $filename = null;
    private $erase = false;

    public function __construct(Directory $directory, $filename = null, $erase = false)
    {
        $this->directory = $directory;
        $this->setErase($erase);
        if (null !== $filename) {
            $this->setFilename($filename);
        }
    }


    public function setFilename($filename)
    {
        $this->filename = basename($filename);
    }


    public function getRealPathname()
    {
        return $this->directory->getDirectory().DIRECTORY_SEPARATOR.$this->filename;
    }


    public function setErase($erase)
    {
        $this->erase = (bool)$erase;
    }


    public function save($data)
    {
        $options = true === $this->erase ? null : FILE_APPEND;

        if (file_put_contents($this->getRealPathname(), $data, $options) === false) {
            return false;
        }

        return true;
    }


    public function getContent()
    {
        if (file_exists($this->getRealPathname()) === false || is_readable($this->getRealPathname()) === false) {
            throw new \RuntimeException($this->getRealPathname().' isn\'t readable');
        }

        return file_get_contents($this->getRealPathname());
    }
}