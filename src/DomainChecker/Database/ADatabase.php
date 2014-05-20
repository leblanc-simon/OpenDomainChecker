<?php

namespace DomainChecker\Database;
use DomainChecker\Core\Config;
use ZipArchive;

abstract class ADatabase
{
    /**
     * @var array
     */
    protected $available_types = array('server', 'domain');

    /**
     * @var array
     */
    protected $servers = array();

    /**
     * @var array
     */
    protected $domains = array();


    /**
     * Add a server to save
     *
     * @param   string  $ip     the IP address of the server
     * @param   array   $values the values to save (key => value)
     * @return  $this
     */
    public function addServer($ip, $values)
    {
        if (isset($this->servers[$ip]) === true) {
            $values = array_merge($this->servers[$ip], $values);
        }

        $this->servers[$ip] = $values;
        ksort($this->servers);

        return $this;
    }


    /**
     * Add a domain name to save
     *
     * @param   string  $domain the domain name
     * @param   array   $values the values to save (key => value)
     * @return  $this
     */
    public function addDomain($domain, $values)
    {
        if (isset($this->domains[$domain]) === true) {
            $values = array_merge($this->domains[$domain], $values);
        }

        $this->domains[$domain] = $values;
        ksort($this->domains);

        return $this;
    }


    /**
     * Return all servers
     *
     * @return  array   an array with all servers
     */
    public function getServers()
    {
        return $this->servers;
    }


    /**
     * Return all domains
     *
     * @return  array   an array with all domains
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * Create a zip archive which contains putty files
     * @param $zip_file
     * @return bool
     */
    public function generatePuttyFiles($zip_file)
    {
        $named_servers = array();
        $example_file = Config::get('data_dir').'/putty_example';
        $putty_dir=Config::get('data_dir').'/putty';
        $zip = new ZipArchive;

        if (!file_exists($example_file)) {
            return false;
        }

        if (!is_dir($putty_dir)) {
            mkdir($putty_dir);
        }
        /* Recupere les serveurs nommes et les ajoute au fichier zip */
        foreach ($this->servers as $ip => $values) {

            if (isset($this->servers[$ip]['nom']) === true && ($this->servers[$ip]['nom'] !== '')) {
                $name = $this->servers[$ip]['nom'];
                $name = str_replace(' ','%20',$name);
                $named_servers[$name] = $this->servers[$ip];
                $file_content = file_get_contents($example_file);

                /* On change l'ip de l'exemple */
                $file_content = str_replace('%%ip%%',$ip,$file_content);

                /* On creer le fichier, on le met dans l'archive puis le supprime */
                file_put_contents($putty_dir.'/'.$name, $file_content);
                $zip_dir = $zip->open($zip_file, ZipArchive::CREATE);
                if ($zip_dir === TRUE) {
                    $zip->addFile($putty_dir.'/'.$name, $name);
                    $zip->close();
                }
                unlink($putty_dir.'/'.$name);
            }
        }
        return true;
    }
}