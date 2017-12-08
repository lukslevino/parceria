<?php

namespace Base\Entity\Repository;

use Base\Exception\ServiceException;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;

abstract class AbstractEntityRepository extends EntityRepository implements EntityRepositoryInterface
{
    /**
     * @var EntityManager
     */
    protected $validator;

    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);

        /**
         * @deprecated O processo abaixo deve ser realizado no Driver e não nessa classe,
         * a conexão já deve vir com essas alterações definidas.
         */
        $db = $em->getConnection();
        $driver = $db->getDriver();
        if ($driver instanceof \Doctrine\DBAL\Driver\OCI8\Driver) {
            $db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
            $db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
            $db->query("ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '.,'");
        }
    }

    // ---- SAVE -------------------------------------------------------------------------------------------------------

    /**
     * Roteador para insert e update
     *
     * @param array $data
     * @param interger|array $id
     * @return boolean
     */
    public function save(array $data, $id = null)
    {
        if (!$data && !$id) {
            throw new \InvalidArgumentException('Não foi possível encontrar dados para persistir.');
        }

        $entityManager = $this->getEntityManager();
        $entityName = $this->getEntityName();
        if ($id) {
            $entity = $entityManager->getReference($entityName, $id);
            $hydrator = new ClassMethods();
            $hydrator->hydrate($data, $entity);
        } else {
            $entity = new $entityName($data);
        }

        $entity->resolveAssociations($entityManager);

        // Pré
        if ($id) {
            $this->preUpdate($entity);
        } else {
            $this->preInsert($entity);
        }

        $entityManager->persist($entity);
        $entityManager->flush();
        $entityManager->clear();

        // Pos
        if ($id) {
            $this->postUpdate($entity);
        } else {
            $this->postInsert($entity);
        }

        return $entity;
    }

    // ---- INSERT -----------------------------------------------------------------------------------------------------

    /**
     * Efetua operação antes de executar o insert
     */
    protected function preInsert($entity)
    {
        if (null == $this->validator) {
            return;
        }

        $validator = new $this->validator;
        if (!$validator->isValid($entity)) {
            throw new ServiceException($validator->getMessages());
        }
    }

    /**
     * Efetua operações depois de executar o insert
     */
    protected function postInsert($entity)
    {
    }

    // ---- UDPATE -----------------------------------------------------------------------------------------------------

    /**
     * Efetua operações antes de executar o update
     */
    protected function preUpdate($entity)
    {
    }

    /**
     * Efetua operações depois de executar o update
     */
    protected function postUpdate($entity)
    {
    }

    // ---- DELETE -----------------------------------------------------------------------------------------------------

    /**
     * Efetua operações antes de executar o delete
     */
    protected function preDelete($reference)
    {
    }

    /**
     * Apaga o registro "id" do banco
     *
     * @param integer|array $id
     * @return void
     */
    public function delete($id)
    {
        $reference = $this->getEntityManager()->getReference($this->getEntityName(), $id);
        if ($reference) {
            $this->preDelete($reference);

            $this->getEntityManager()->remove($reference);
            $this->getEntityManager()->flush();

            $this->postDelete($reference);
        }
    }

    /**
     * Efetua operações depois de executar o delete
     */
    protected function postDelete($reference)
    {
    }

    /**
     * Função responsável pela paginação de dados
     *
     * @deprecated Essa função terá a visibilidade reduzida para protected na próxima versão.
     *
     * @param string $query
     * @param integer $page
     * @param integer $limit
     *
     * @return Zend\Paginator\Paginator
     */
    public function paginator($query, $page, $limit = 10)
    {
        $em = $this->getEntityManager();
        $adapter = new DoctrineAdapter(new ORMPaginator($em->createQuery($query)));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage($limit);
        $page ? $paginator->setCurrentPageNumber($page) : null;

        return $paginator;
    }
}
