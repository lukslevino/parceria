<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * UsuarioFuncaoEntity
 *
 * @ORM\Table(name="tb_ol_usuario_funcao", uniqueConstraints={@ORM\UniqueConstraint(name="CO_USUARIO_FUNCAO_UNIQUE", columns={"CO_USUARIO_FUNCAO"})}, indexes={@ORM\Index(name="fk_TB_OL_USUARIO_FUNCAO_TB_OL_USUARIO_idx", columns={"CO_USUARIO"}), @ORM\Index(name="fk_TB_OL_USUARIO_FUNCAO_TB_OL_FUNCAO1_idx", columns={"CO_FUNCAO"}), @ORM\Index(name="fk_TB_OL_USUARIO_FUNCAO_TB_OL_SITUACAO_USU_FUNCAO1_idx", columns={"CO_SITUACAO_USU_FUNCAO"})})
 * @ORM\Entity
 */
class UsuarioFuncaoEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CO_USUARIO_FUNCAO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $coUsuarioFuncao;

    /**
     * @var string
     *
     * @ORM\Column(name="ST_AUTORIZACAO", type="string", length=1, nullable=false)
     */
    private $stAutorizacao = 'P';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DT_INCLUSAO", type="date", nullable=false)
     */
    private $dtInclusao;

    /**
     * @var \FuncaoEntity
     *
     * @ORM\ManyToOne(targetEntity="FuncaoEntity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CO_FUNCAO", referencedColumnName="CO_FUNCAO")
     * })
     */
    private $coFuncao;

    /**
     * @var \SituacaoUsuFuncaoEntity
     *
     * @ORM\ManyToOne(targetEntity="SituacaoUsuFuncaoEntity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CO_SITUACAO_USU_FUNCAO", referencedColumnName="CO_SITUACAO_USU_FUNCAO")
     * })
     */
    private $coSituacaoUsuFuncao;

    /**
     * @var \UsuarioEntity
     *
     * @ORM\ManyToOne(targetEntity="UsuarioEntity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CO_USUARIO", referencedColumnName="CO_USUARIO")
     * })
     */
    private $coUsuario;

    /**
     * @return int
     */
    public function getCoUsuarioFuncao()
    {
        return $this->coUsuarioFuncao;
    }

    /**
     * @param int $coUsuarioFuncao
     */
    public function setCoUsuarioFuncao($coUsuarioFuncao)
    {
        $this->coUsuarioFuncao = $coUsuarioFuncao;
    }

    /**
     * @return string
     */
    public function getStAutorizacao()
    {
        return $this->stAutorizacao;
    }

    /**
     * @param string $stAutorizacao
     */
    public function setStAutorizacao($stAutorizacao)
    {
        $this->stAutorizacao = $stAutorizacao;
    }

    /**
     * @return DateTime
     */
    public function getDtInclusao()
    {
        return $this->dtInclusao;
    }

    /**
     * @param DateTime $dtInclusao
     */
    public function setDtInclusao($dtInclusao)
    {
        $this->dtInclusao = $dtInclusao;
    }

    /**
     * @return FuncaoEntity
     */
    public function getCoFuncao()
    {
        return $this->coFuncao;
    }

    /**
     * @param FuncaoEntity $coFuncao
     */
    public function setCoFuncao($coFuncao)
    {
        $this->coFuncao = $coFuncao;
    }

    /**
     * @return SituacaoUsuFuncaoEntity
     */
    public function getCoSituacaoUsuFuncao()
    {
        return $this->coSituacaoUsuFuncao;
    }

    /**
     * @param SituacaoUsuFuncaoEntity $coSituacaoUsuFuncao
     */
    public function setCoSituacaoUsuFuncao($coSituacaoUsuFuncao)
    {
        $this->coSituacaoUsuFuncao = $coSituacaoUsuFuncao;
    }

    /**
     * @return UsuarioEntity
     */
    public function getCoUsuario()
    {
        return $this->coUsuario;
    }

    /**
     * @param UsuarioEntity $coUsuario
     */
    public function setCoUsuario($coUsuario)
    {
        $this->coUsuario = $coUsuario;
    }


}

