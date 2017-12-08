<?php

namespace Application\Entity\Repository;

use Application\Entity\DmIes;
use Base\Entity\Repository\AbstractEntityRepository;
use Doctrine\ORM\Query\Expr;
use Application\Util\StringUtil;
use Application\Entity\TbSnrUsuario;

class UsuarioRepository extends AbstractEntityRepository
{
    /**
     * @param $post
     * @param null $identity
     * @return array
     */
    public function findAllByPost($post, $identity = null)
    {
        $qb = $this->createQueryBuilder('u')->select('u.coUsuario,
            u.noUsuario,
            u.nuCpf,
            f.noFuncao,
            uf.coUsuarioFuncao,
            uf.stAutorizacao,
            suf.noSituacaoUsuFuncao,
            i.noIes'
        );
        $qb->innerJoin('Application\Entity\TbSnrUsuarioFuncao', 'uf', Expr\Join::WITH, 'u.coUsuario = uf.coUsuario');
        $qb->innerJoin('Application\Entity\TbSnrFuncao', 'f', Expr\Join::WITH, 'f.coFuncao = uf.coFuncao');
        $qb->innerJoin(
            'Application\Entity\TbSnrSituacaoUsuFuncao',
            'suf',
            Expr\Join::WITH,
            'suf.coSituacaoUsuFuncao = uf.coSituacaoUsuFuncao'
        );
        $qb->leftJoin('Application\Entity\DmIes', 'i', Expr\Join::WITH, 'i.coDmIes = uf.coDmIes');

        if (!empty($post->noUsuario)) {
            $qb->andWhere("UPPER(u.noUsuario) LIKE '%" . mb_strtoupper(trim($post->noUsuario), "UTF-8") . "%'");
        }
        if (!empty($post->nuCpf)) {
            $qb->andWhere("u.nuCpf LIKE '" . StringUtil::removeMascara($post->nuCpf) . "'");
        }
        if (!empty($post->stAutorizacao)) {
            $qb->andWhere("uf.coSituacaoUsuFuncao = " . $post->stAutorizacao);
        }
        if (!empty($post->coFuncao)) {
            $qb->andWhere("f.coFuncao = '" . $post->coFuncao . "'");
        }
        if (!empty($post->arCoDmIes)) {
            $qb->andWhere("uf.coDmIes IN (" . implode(',', $post->arCoDmIes) . ")");
        }
        if ($identity) {
            $qb->andWhere($qb->expr()->neq('u.coUsuario', $identity->coUsuario));
        }

        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Armazena um registro que vem do SSD
     * @param \stdClass $data
     * @return TbSnrUsuario
     */
    public function saveSsd($data)
    {
        $dataAtual = new \DateTime("now");

        $object = new TbSnrUsuario();
        $object->setNuCnpj($data->cnpj);
        $object->setNuCpf($data->cpf);
        $object->setNoUsuario($data->name);
        $object->setDsEmail($data->email);
        $object->setDsEmailAlternativo($data->alternativeEmail);
        $object->setCoUsuarioSsd($data->userId);
        $object->setDtInclusao($dataAtual);
        $object->setDtUltimoAcesso($dataAtual);

        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();

        return $object;
    }

    /**
     * Lista todos os usuários da Instituição com perfis Reitor, Gestor Instituição e Usuário Instituição
     * exceto o próprio usuário sendo editado
     * @param DmIes $coDmIes
     * @param TbSnrUsuario $coUsuario
     * @return array
     */
    public function listarUsuariosPorIes($coDmIes, $coUsuario)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u.coUsuario, u.noUsuario, f.noFuncao, uf.coUsuarioFuncao')
            ->innerJoin('Application\Entity\TbSnrUsuarioFuncao', 'uf', Expr\Join::WITH, 'u.coUsuario = uf.coUsuario')
            ->innerJoin('Application\Entity\TbSnrFuncao', 'f', Expr\Join::WITH, 'uf.coFuncao = f.coFuncao')
            ->andWhere($qb->expr()->in('uf.coFuncao', array(4, 5, 6)))
            ->andWhere($qb->expr()->eq('uf.coDmIes', $coDmIes->getCoDmIes()))
            ->andWhere($qb->expr()->notIn('u.coUsuario', $coUsuario->getCoUsuario()));

        return $qb->getQuery()->getResult();
    }
}
