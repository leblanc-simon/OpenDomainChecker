<?php

namespace DomainChecker\Filesystem;

class Csv implements \Iterator
{
    private $separator  = ';';
    private $enclosure  = '"';
    private $length     = 1000;

    /**
     * @var File
     */
    protected $file       = null;

    /**
     * @var resource
     */
    private $handle     = null;

    protected $current_line = false;
    protected $current_position = null;

    public function __construct(File $file)
    {
        $this->file = $file;
        $this->current_position = 0;
    }


    protected function getLine()
    {
        if (null === $this->handle) {
            $this->handle = fopen($this->file->getRealPathname(), 'rb');
            if (false === $this->handle) {
                $this->handle = null;
                throw new \Exception('Impossible to open '.$this->file->getRealPathname());
            }
        }

        return fgetcsv($this->handle, $this->length, $this->separator, $this->enclosure);
    }


    /**
     * @param string $enclosure
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @param string $separator
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
    }


    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->current_line;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->current_line = $this->getLine();
        if (false !== $this->current_line) {
            $this->current_position++;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->current_position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return (bool)$this->current_line;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->current_line = $this->getLine();
        $this->current_position = 0;
    }
}