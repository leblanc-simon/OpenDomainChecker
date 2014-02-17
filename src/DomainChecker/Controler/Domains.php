<?php

namespace DomainChecker\Controler;

class Domains extends AControler
{
    public function get()
    {
        return $this->renderJson($this->database->getDomains());
    }


    public function show($name)
    {
        $domains = $this->database->getDomains();
        if (isset($domains[$name]) === false) {
            return $this->renderError('Impossible to find domain');
        }

        return $this->renderJson($domains[$name]);
    }
}