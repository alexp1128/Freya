<?php

return array(
    'mode'                  => 'development',
    'debug'                 => true,
    
    'log.enabled'           => true,
    'log.writer'            => null,
    'log.level'             => \Psr\Log\LogLevel::DEBUG,
    
    'path.base'             => $basePath,
    'path.app'              => $basePath . '/app',
    'path.vendor'           => $basePath . '/vendor',
    'path.storage'          => $basePath . '/storage',
    'path.public'           => $basePath . '/public_html',
    
    'templates.path'        => './templates',
    'view'                  => '\Slim\View',
    
    'cookies.encrypt'       => false,
    'cookies.lifetime'      => '20 minutes',
    'cookies.path'          => '/',
    'cookies.domain'        => null,
    'cookies.secure'        => false,
    'cookies.httponly'      => false,
    'cookies.secret_key'    => 'CHANGE_ME',
    'cookies.cipher'        => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode'   => MCRYPT_MODE_CBC,
    
    'http.version'          => '1.1',
    'routes.case_sensitive' => true,
    'timezone'              => 'America/Chicago'
);
