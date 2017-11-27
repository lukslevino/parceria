<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors', true);
ini_set('display_errors', true);
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);

return array(
    'log' => array(
        'path' => __DIR__ . '/../../data/logs/'
    ),
    'upload_path' => array(
        'upload'     => 'data\upload',
        'termo'      => '/../data/termos/',
        'tmpuploads' => '/../data/tmpuploads/',
    ),
    'path' => array(
        'upload' => getcwd(),
        'temp'   => sys_get_temp_dir(),
    ),
    'mail' => array(
        'debug' => true,
        'file_options' => array(
            'path' => __DIR__ . '/../../data/mail',
            'callback' => function () {
                return 'Mail_' . microtime(true) . '_' . mt_rand() . '.html';
            },
        )
    ),
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'params' => array(
                    //MEC
                    'host'     => 'examec01-scan4.mec.gov.br',
                   //'host' => '10.37.0.208',
                    //SALVADOR
                    //'host'     => '192.168.44.41',
                    'user'     => 'SYSDBCAROLINABORI',
                    'password' => 'SYSDBCAROLINABORI',
                    //'dbname'   => 'dsvora',
                    //'dbname'    => 'hmgora',
                   'dbname'    => 'tstora',
                    //'dbname'   => 'tstora.mec.gov.br',
                    //'dbname'   => 'treora.mec.gov.br',
                )
            )
        ),
        'configuration' => array(
            'orm_default' => array(
                'proxy_dir' => __DIR__.'/../../data/DoctrineORMModule/Proxy',
            )
        ),
    ),
    'db' => array(
        'driver'        => 'OCI8',
        //MEC
        'hostname'      => '10.37.0.193/dsvora.mec.gov.br',
        //SALVADOR
        //'hostname'      => '192.168.44.41/dsvora.mec.gov.br',
        'character_set' => 'AL32UTF8',
        'username'      => 'SYSDBREVALIDACAO',
        'password'      => 'SYSDBREVALIDACAO',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'user_identify' => array(
        'audit_schema' => 'DBREVALIDACAO_AUDITORIA',
    ),
    // property
    'smtp_options' => array(
        'name' => 'smtp6',
        'port' => 25,
        'host' => 'smtp6.mec.gov.br',
        'connection_class' => 'login',
        'connection_config' => array(
            'username' => '',
            'password' => '',
            'ssl'      => 'tls'
        )
    ),
    'ssd' => array(
        'info' => array(
            'codebase'                 => 'http://ssddev.mec.gov.br',
            'applet'                   => 'http://ssddev.mec.gov.br/applet/ssd-applet.jar',
            'fileUpload'               => 'https://ssddev.mec.gov.br/ssd-server/servlet/UploadTmpDoc',
            'converterToPdf'           => 'http://wshmg.mec.gov.br/ws-server/htmlParaPdf',
            'downloadSignaturePackage' => 'https://ssddev.mec.gov.br/ssd-server/servlet/DownloadSignaturePackage',
            'wsdl' => array(
                'auth'       => array (
                    'https://ssddev.mec.gov.br/ssd-server/services/Authentication?wsdl',
                    array(
                        'stream_context' => array(
                            'ssl' => array(
                                'verify_peer' => false
                            )
                        )
                    )
                ),
                'sign'       => 'https://ssddev.mec.gov.br/ssd-server/services/Signature?wsdl',
                'user'       => 'https://ssddev.mec.gov.br/ssd-server/services/UserMaintenance?wsdl',
                'manutencao' => 'https://ssddev.mec.gov.br/wsdl/ManutencaoUsuarioWS?wsdl'
            )
        ),
        'global_options' => array(
            'stream_context' => array(
                'ssl' => array(
                    'verify_peer' => false
                )
            )
        ),
        'files' => array(
            'certificate' => 'data' . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'certificate.pem',
            'key' => 'data' . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'key.pem',
            'chain' => 'data' . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'chain.pem',
            'password' => 'data' . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'password.pem',
            'credential' => 'data' . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'credential.pem'
        )
    ),
    'capes' => [
        'cadastro-iesestrangeira' => [
            'uri' => 'http://servicosies.hom.capes.gov.br:90/instituicoes-servico/rest/solicitacoes/instituicao/estrangeira/',
            'type' => 'post',
            'method' => 'cadastrar',
            'uri_extra_params' => []
        ],
        'consulta-cadastro-iesestrangeira' => [
            'uri' => 'http://servicosies.hom.capes.gov.br:90/instituicoes-servico/rest/solicitacoes/solicitante/:identificador/',
            'type' => 'get',
            'method' => 'consultar',
            'uri_extra_params' => ['identificador']
        ],
        'consulta-iesestrangeira' => [
            'uri' => 'http://servicosies.hom.capes.gov.br:80/instituicoes-servico/rest/instituicoes/estrangeira/',
            'type' => 'get',
            'method' => 'consultar',
            'uri_extra_params' => []
        ],
    ],
);
