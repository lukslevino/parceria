<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * SituacaoUsuFuncaoEntity
 *
 * @ORM\Table(name="tb_ol_situacao_usu_funcao", uniqueConstraints={@ORM\UniqueConstraint(name="CO_SITUACAO_USU_FUNCAO_UNIQUE", columns={"CO_SITUACAO_USU_FUNCAO"})})
 * @ORM\Entity
 */
class SituacaoUsuFuncaoEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CO_SITUACAO_USU_FUNCAO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $coSituacaoUsuFuncao;

    /**
     * @var string
     *
     * @ORM\Column(name="NO_SITUACAO_USU_FUNCAO", type="string", length=50, nullable=false)
     */
    private $noSituacaoUsuFuncao;

    /**
     * @var string
     *
     * @ORM\Column(name="DS_SITUACAO_USU_FUNCAO", type="string", length=255, nullable=false)
     */
    private $dsSituacaoUsuFuncao;

    /**
     * @return int
     */
    public function getCoSituacaoUsuFuncao()
    {
        return $this->coSituacaoUsuFuncao;
    }

    /**
     * @param int $coSituacaoUsuFuncao
     */
    public function setCoSituacaoUsuFuncao($coSituacaoUsuFuncao)
    {
        $this->coSituacaoUsuFuncao = $coSituacaoUsuFuncao;
    }

    /**
     * @return string
     */
    public function getNoSituacaoUsuFuncao()
    {
        return $this->noSituacaoUsuFuncao;
    }

    /**
     * @param string $noSituacaoUsuFuncao
     */
    public function setNoSituacaoUsuFuncao($noSituacaoUsuFuncao)
    {
        $this->noSituacaoUsuFuncao = $noSituacaoUsuFuncao;
    }

    /**
     * @return string
     */
    public function getDsSituacaoUsuFuncao()
    {
        return $this->dsSituacaoUsuFuncao;
    }

    /**
     * @param string $dsSituacaoUsuFuncao
     */
    public function setDsSituacaoUsuFuncao($dsSituacaoUsuFuncao)
    {
        $this->dsSituacaoUsuFuncao = $dsSituacaoUsuFuncao;
    }


}

