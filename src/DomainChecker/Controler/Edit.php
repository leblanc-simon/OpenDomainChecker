<?php

namespace DomainChecker\Controler;

use DomainChecker\Core\Config;
use Symfony\Component\HttpFoundation\Request;

class Edit extends AControler
{
    public function save(Request $request)
    {
        $type = $request->get('_type');
        
        if ('servers' === $type) {
            $return = $this->saveServer($request);
        } elseif ('domains' === $type) {
            $return = $this->saveDomain($request);
        } else {
            return $this->renderError('Impossible to save datas : neither domain or server');
        }

        return $return;
    }


    private function saveServer(Request $request)
    {
        return $this->saveInternal($request, 'addServer');
    }


    private function saveDomain(Request $request)
    {
        return $this->saveInternal($request, 'addDomain');
    }

    
    private function saveInternal(Request $request, $method)
    {
        $additionnal_fields = explode(',', Config::get('additionnal_fields'));

        $values = array();
        foreach ($additionnal_fields as $field) {
            $values[$field] = $request->get($field);
        }

        $this->database->$method($request->get('_id'), $values);
        $this->database->save();

        return $this->renderJson(array('success' => true));
    }
}