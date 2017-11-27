<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors', true);
ini_set('display_errors', true);
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);


return array(
    /*'doctrine' => array(
        'configuration' => [
            'orm_default' => [
                'numeric_functions' => [
                    'ROUND' => 'Application\DoctrineExtensions\Round',
                    'TO_DATE' => 'Application\DoctrineExtensions\ToDate',
                    'TO_CHAR' => 'Application\DoctrineExtensions\ToChar',
                    'YEAR' => 'Application\DoctrineExtensions\Year',
                    'MONTH' => 'Application\DoctrineExtensions\Month',
                ],
            ],
        ],
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\OCI8\Driver',
                'params' => array(
                    'port'     => '1521',
                    'service'  => true,
                    'charset'  => 'utf8',
                )
            )
        ),
    ),*/
    'log' => array(
        'path' => __DIR__.'/../../data/logs/'
   ),
    /*
    'environment' => 'development',
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'params' => array(
                    'host'     => '192.168.44.41',
                    'user'     => 'DBREVALIDACAO',
                    'password' => 'DBREVALIDACAO',
                    'dbname'   => 'dsvora.mec.gov.br',
                )
            )
        ),
    ),*/
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=dbortholife;host=127.0.0.1',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
        )
    )
);
