<?php

namespace DomainChecker\Controler;

class Servers extends AControler
{
    public function get()
    {
        return $this->renderJson($this->database->getServers());
    }


    public function show($ip)
    {
        $servers = $this->database->getServers();
        if (isset($servers[$ip]) === false) {
            return $this->renderError('Impossible to find server');
        }

        return $this->renderJson($servers[$ip]);
    }
}