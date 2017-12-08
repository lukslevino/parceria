<?php

namespace Application\Service;

/**
 * @deprecated Usar o serviço Commons\Pattern\Service\Impl\AbstractCoreService
 */
abstract class AbstractService
{
    protected $sm;
    protected $em;
    protected $log;

    public function __construct($sm, $em, $log)
    {
        $this->sm  = $sm;
        $this->em  = $em;
        $this->log = $log;
    }

    public function getServiceManager()
    {
        return $this->sm;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function getLogger()
    {
        return $this->log;
    }
}
