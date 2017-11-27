<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ParceiroEntity
 *
 * @ORM\Table(name="tb_ol_parceiro", uniqueConstraints={@ORM\UniqueConstraint(name="CO_PARCEIRO_UNIQUE", columns={"CO_PARCEIRO"})}, indexes={@ORM\Index(name="fk_TB_OL_PARCEIRO_TB_OL_USUARIO_FUNCAO1_idx", columns={"CO_USUARIO_FUNCAO"}), @ORM\Index(name="fk_TB_OL_PARCEIRO_TB_OL_UNIDADE1_idx", columns={"CO_UNIDADE"})})
 * @ORM\Entity
 */
class ParceiroEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CO_PARCEIRO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $coParceiro;

    /**
     * @var integer
     *
     * @ORM\Column(name="CO_MUNICIPO", type="integer", nullable=false)
     */
    private $coMunicipo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DT_NASCIMENTO", type="date", nullable=false)
     */
    private $dtNascimento;

    /**
     * @var string
     *
     * @ORM\Column(name="DS_LOGRADOURO", type="string", length=255, nullable=false)
     */
    private $dsLogradouro;

    /**
     * @var string
     *
     * @ORM\Column(name="NU_CEP", type="string", length=8, nullable=false)
     */
    private $nuCep;

    /**
     * @var string
     *
     * @ORM\Column(name="TP_SEXO", type="string", length=1, nullable=false)
     */
    private $tpSexo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DT_INCLUSAO", type="date", nullable=false)
     */
    private $dtInclusao;

    /**
     * @var string
     *
     * @ORM\Column(name="NU_ENDERECO", type="string", length=10, nullable=false)
     */
    private $nuEndereco;

    /**
     * @var string
     *
     * @ORM\Column(name="DS_COMPLEMENTO_ENDERECO", type="string", length=100, nullable=false)
     */
    private $dsComplementoEndereco;

    /**
     * @var string
     *
     * @ORM\Column(name="NO_BAIRRO", type="string", length=50, nullable=true)
     */
    private $noBairro;

    /**
     * @var string
     *
     * @ORM\Column(name="TIPO_LOGRADOURO", type="string", length=50, nullable=true)
     */
    private $tipoLogradouro;

    /**
     * @var \UnidadeEntity
     *
     * @ORM\ManyToOne(targetEntity="UnidadeEntity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CO_UNIDADE", referencedColumnName="CO_UNIDADE")
     * })
     */
    private $coUnidade;

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
    public function getCoParceiro()
    {
        return $this->coParceiro;
    }

    /**
     * @param int $coParceiro
     */
    public function setCoParceiro($coParceiro)
    {
        $this->coParceiro = $coParceiro;
    }

    /**
     * @return int
     */
    public function getCoMunicipo()
    {
        return $this->coMunicipo;
    }

    /**
     * @param int $coMunicipo
     */
    public function setCoMunicipo($coMunicipo)
    {
        $this->coMunicipo = $coMunicipo;
    }

    /**
     * @return DateTime
     */
    public function getDtNascimento()
    {
        return $this->dtNascimento;
    }

    /**
     * @param DateTime $dtNascimento
     */
    public function setDtNascimento($dtNascimento)
    {
        $this->dtNascimento = $dtNascimento;
    }

    /**
     * @return string
     */
    public function getDsLogradouro()
    {
        return $this->dsLogradouro;
    }

    /**
     * @param string $dsLogradouro
     */
    public function setDsLogradouro($dsLogradouro)
    {
        $this->dsLogradouro = $dsLogradouro;
    }

    /**
     * @return string
     */
    public function getNuCep()
    {
        return $this->nuCep;
    }

    /**
     * @param string $nuCep
     */
    public function setNuCep($nuCep)
    {
        $this->nuCep = $nuCep;
    }

    /**
     * @return string
     */
    public function getTpSexo()
    {
        return $this->tpSexo;
    }

    /**
     * @param string $tpSexo
     */
    public function setTpSexo($tpSexo)
    {
        $this->tpSexo = $tpSexo;
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
     * @return string
     */
    public function getNuEndereco()
    {
        return $this->nuEndereco;
    }

    /**
     * @param string $nuEndereco
     */
    public function setNuEndereco($nuEndereco)
    {
        $this->nuEndereco = $nuEndereco;
    }

    /**
     * @return string
     */
    public function getDsComplementoEndereco()
    {
        return $this->dsComplementoEndereco;
    }

    /**
     * @param string $dsComplementoEndereco
     */
    public function setDsComplementoEndereco($dsComplementoEndereco)
    {
        $this->dsComplementoEndereco = $dsComplementoEndereco;
    }

    /**
     * @return string
     */
    public function getNoBairro()
    {
        return $this->noBairro;
    }

    /**
     * @param string $noBairro
     */
    public function setNoBairro($noBairro)
    {
        $this->noBairro = $noBairro;
    }

    /**
     * @return string
     */
    public function getTipoLogradouro()
    {
        return $this->tipoLogradouro;
    }

    /**
     * @param string $tipoLogradouro
     */
    public function setTipoLogradouro($tipoLogradouro)
    {
        $this->tipoLogradouro = $tipoLogradouro;
    }

    /**
     * @return UnidadeEntity
     */
    public function getCoUnidade()
    {
        return $this->coUnidade;
    }

    /**
     * @param UnidadeEntity $coUnidade
     */
    public function setCoUnidade($coUnidade)
    {
        $this->coUnidade = $coUnidade;
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

