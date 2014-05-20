<?php

namespace DomainChecker\Controler;

use DomainChecker\Core\Config;

class Homepage extends AControler
{
    public function get()
    {
        $layout = file_get_contents(Config::get('data_dir').'/template/layout.html');

        $routes = array(
            'homepage',
            'servers',
            'domains',
            'export-all',
            'export-servers',
            'export-domains',
            'edit',
            'import',
            'putty',
        );

        foreach ($routes as $route) {
            $layout = str_replace(
                '%route-'.$route.'%',
                $this->application['url_generator']->generate($route),
                $layout
            );
        }

        $layout = str_replace('%additionnal_fields_domain%', json_encode(explode(',' ,Config::get('additionnal_fields_domain'))), $layout);
        $layout = str_replace('%additionnal_fields_server%', json_encode(explode(',' ,Config::get('additionnal_fields_server'))), $layout);
        
        return $layout;
    }
}