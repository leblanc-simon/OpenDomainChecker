<?php

namespace DomainChecker\Filesystem;

use Symfony\Component\Finder\Finder;

class Directory
{
    /**
     * @var string
     */
    private $directory = null;

    /**
     * Constructor
     *
     * @param string    $directory  the path of the directory
     */
    public function __construct($directory)
    {
        $this->checkOrCreate($directory);
    }

    /**
     * Check if the directory exists or try to create it.
     *
     * @param   string  $directory  the path of the directory to check
     * @throws  \RuntimeException   if the directory doesn't exist AND fail to create
     */
    public function checkOrCreate($directory)
    {
        if (is_dir($directory) === false) {
            if (@mkdir($directory, 0777, true) === false) {
                throw new \RuntimeException('Impossible to create directory');
            }
        }

        $this->directory = realpath($directory);
    }


    /**
     * Return the real pathname of the current directory
     *
     * @return  string  The real pathname
     */
    public function getDirectory()
    {
        return $this->directory;
    }


    /**
     * Clear the current directory (remove all files)
     *
     * @throws \RuntimeException    If the remove fail
     */
    public function clear()
    {
        $files = Finder::create()->files()->in($this->directory);

        foreach ($files as $file) {
            if (@unlink($file->getRealpath()) === false) {
                throw new \RuntimeException('Impossible to delete '.$file->getRealpath());
            }
        }
    }


    /**
     * Return all files in the current directory
     *
     * @return array<File>  All files in the directory
     */
    public function getAll()
    {
        $files = Finder::create()->files()->in($this->directory);

        $all = array();

        foreach ($files as $file) {
            $ofile = new File($this, $file->getBasename());
            $all[] = $ofile;
        }

        return $all;
    }
}