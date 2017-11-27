<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * UsuarioContatoEntity
 *
 * @ORM\Table(name="tb_ol_usuario_contato", indexes={@ORM\Index(name="fk_TB_USUARIO_CONTATO_TB_OL_USUARIO_FUNCAO1_idx", columns={"CO_USUARIO_FUNCAO"})})
 * @ORM\Entity
 */
class UsuarioContatoEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CO_USUARIO_CONTATO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $coUsuarioContato;

    /**
     * @var string
     *
     * @ORM\Column(name="DS_CONTATO", type="string", length=255, nullable=false)
     */
    private $dsContato;

    /**
     * @var string
     *
     * @ORM\Column(name="TP_CONTATO", type="string", length=45, nullable=false)
     */
    private $tpContato;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DT_INCLUSAO", type="date", nullable=false)
     */
    private $dtInclusao;

    /**
     * @var \UsuarioFuncaoEntity
     *
     * @ORM\ManyToOne(targetEntity="UsuarioFuncaoEntity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CO_USUARIO_FUNCAO", referencedColumnName="CO_USUARIO_FUNCAO")
     * })
     */
    private $coUsuarioFuncao;

    /**
     * @return int
     */
    public function getCoUsuarioContato()
    {
        return $this->coUsuarioContato;
    }

    /**
     * @param int $coUsuarioContato
     */
    public function setCoUsuarioContato($coUsuarioContato)
    {
        $this->coUsuarioContato = $coUsuarioContato;
    }

    /**
     * @return string
     */
    public function getDsContato()
    {
        return $this->dsContato;
    }

    /**
     * @param string $dsContato
     */
    public function setDsContato($dsContato)
    {
        $this->dsContato = $dsContato;
    }

    /**
     * @return string
     */
    public function getTpContato()
    {
        return $this->tpContato;
    }

    /**
     * @param string $tpContato
     */
    public function setTpContato($tpContato)
    {
        $this->tpContato = $tpContato;
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
     * @return UsuarioFuncaoEntity
     */
    public function getCoUsuarioFuncao()
    {
        return $this->coUsuarioFuncao;
    }

    /**
     * @param UsuarioFuncaoEntity $coUsuarioFuncao
     */
    public function setCoUsuarioFuncao($coUsuarioFuncao)
    {
        $this->coUsuarioFuncao = $coUsuarioFuncao;
    }


}

