<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * UsuarioEntity
 *
 * @ORM\Table(name="tb_ol_usuario", uniqueConstraints={@ORM\UniqueConstraint(name="CO_USUARIO_UNIQUE", columns={"CO_USUARIO"}), @ORM\UniqueConstraint(name="NU_CPF_UNIQUE", columns={"NU_CPF"})})
 * @ORM\Entity
 */
class UsuarioEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CO_USUARIO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $coUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="NU_CPF", type="string", length=11, nullable=false)
     */
    private $nuCpf;

    /**
     * @var string
     *
     * @ORM\Column(name="NO_USUARIO", type="string", length=200, nullable=false)
     */
    private $noUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="DS_EMAIL", type="string", length=255, nullable=false)
     */
    private $dsEmail;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DT_ULTIMO_ACESSO", type="date", nullable=false)
     */
    private $dtUltimoAcesso;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DT_ALTERACAO", type="date", nullable=false)
     */
    private $dtAlteracao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DT_INCLUSAO", type="date", nullable=false)
     */
    private $dtInclusao;

    /**
     * @var integer
     *
     * @ORM\Column(name="CO_USUARIO_ULTIMA_OPERACAO", type="integer", nullable=false)
     */
    private $coUsuarioUltimaOperacao;

    /**
     * @var string
     *
     * @ORM\Column(name="DS_SENHA", type="string", length=40, nullable=false)
     */
    private $dsSenha;

    /**
     * @return int
     */
    public function getCoUsuario()
    {
        return $this->coUsuario;
    }

    /**
     * @param int $coUsuario
     */
    public function setCoUsuario($coUsuario)
    {
        $this->coUsuario = $coUsuario;
    }

    /**
     * @return string
     */
    public function getNuCpf()
    {
        return $this->nuCpf;
    }

    /**
     * @param string $nuCpf
     */
    public function setNuCpf($nuCpf)
    {
        $this->nuCpf = $nuCpf;
    }

    /**
     * @return string
     */
    public function getNoUsuario()
    {
        return $this->noUsuario;
    }

    /**
     * @param string $noUsuario
     */
    public function setNoUsuario($noUsuario)
    {
        $this->noUsuario = $noUsuario;
    }

    /**
     * @return string
     */
    public function getDsEmail()
    {
        return $this->dsEmail;
    }

    /**
     * @param string $dsEmail
     */
    public function setDsEmail($dsEmail)
    {
        $this->dsEmail = $dsEmail;
    }

    /**
     * @return DateTime
     */
    public function getDtUltimoAcesso()
    {
        return $this->dtUltimoAcesso;
    }

    /**
     * @param DateTime $dtUltimoAcesso
     */
    public function setDtUltimoAcesso($dtUltimoAcesso)
    {
        $this->dtUltimoAcesso = $dtUltimoAcesso;
    }

    /**
     * @return DateTime
     */
    public function getDtAlteracao()
    {
        return $this->dtAlteracao;
    }

    /**
     * @param DateTime $dtAlteracao
     */
    public function setDtAlteracao($dtAlteracao)
    {
        $this->dtAlteracao = $dtAlteracao;
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
     * @return int
     */
    public function getCoUsuarioUltimaOperacao()
    {
        return $this->coUsuarioUltimaOperacao;
    }

    /**
     * @param int $coUsuarioUltimaOperacao
     */
    public function setCoUsuarioUltimaOperacao($coUsuarioUltimaOperacao)
    {
        $this->coUsuarioUltimaOperacao = $coUsuarioUltimaOperacao;
    }

    /**
     * @return string
     */
    public function getDsSenha()
    {
        return $this->dsSenha;
    }

    /**
     * @param string $dsSenha
     */
    public function setDsSenha($dsSenha)
    {
        $this->dsSenha = $dsSenha;
    }


}

