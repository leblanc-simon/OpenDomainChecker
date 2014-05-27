<?php

namespace DomainChecker\Controler;

use DomainChecker\Core\Config;
use Silex\Provider\SessionServiceProvider;

class Homepage extends AControler
{
    public function get()
    {
        $layout = file_get_contents(Config::get('data_dir').'/template/layout.html');

        $this->application->register(new SessionServiceProvider());


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

        if (null === $message = $this->application['session']->get('message')){
            $layout = str_replace('%message-import%',$message,$layout);
            return $layout;
        }
        else
        {
            $layout = str_replace('%message-import%',$message,$layout);
            $this->application['session']->remove('message');
            return $layout;
        }
    }
}