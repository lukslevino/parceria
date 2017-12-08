<?php
return array(
    'modules' => array(
        'Application',
        'DoctrineModule',
        'DoctrineORMModule'

    ),

    'module_listener_options' => array(

        'module_paths' => array(
            realpath(__DIR__ . '/../module'),
            realpath(__DIR__ . '/../vendor')
        ),

       'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
            getenv('APP_CONFIG_FILE') ?: 'config/autoload/desenvolvedor.php',
        ),

     ),

);
