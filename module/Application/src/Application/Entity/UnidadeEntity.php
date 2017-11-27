<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * UnidadeEntity
 *
 * @ORM\Table(name="tb_ol_unidade")
 * @ORM\Entity
 */
class UnidadeEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CO_UNIDADE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $coUnidade;

    /**
     * @var string
     *
     * @ORM\Column(name="NO_UNIDADE", type="string", length=200, nullable=false)
     */
    private $noUnidade;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DT_INCLUSAO", type="date", nullable=false)
     */
    private $dtInclusao;

    /**
     * @return int
     */
    public function getCoUnidade()
    {
        return $this->coUnidade;
    }

    /**
     * @param int $coUnidade
     */
    public function setCoUnidade($coUnidade)
    {
        $this->coUnidade = $coUnidade;
    }

    /**
     * @return string
     */
    public function getNoUnidade()
    {
        return $this->noUnidade;
    }

    /**
     * @param string $noUnidade
     */
    public function setNoUnidade($noUnidade)
    {
        $this->noUnidade = $noUnidade;
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


}

