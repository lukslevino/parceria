<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * FuncaoEntity
 *
 * @ORM\Table(name="tb_ol_funcao", uniqueConstraints={@ORM\UniqueConstraint(name="CO_FUNCAO_UNIQUE", columns={"CO_FUNCAO"})})
 * @ORM\Entity
 */
class FuncaoEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CO_FUNCAO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $coFuncao;

    /**
     * @var string
     *
     * @ORM\Column(name="NO_FUNCAO", type="string", length=50, nullable=false)
     */
    private $noFuncao;

    /**
     * @var string
     *
     * @ORM\Column(name="DS_FUNCAO", type="string", length=100, nullable=false)
     */
    private $dsFuncao;

    /**
     * @return int
     */
    public function getCoFuncao()
    {
        return $this->coFuncao;
    }

    /**
     * @param int $coFuncao
     */
    public function setCoFuncao($coFuncao)
    {
        $this->coFuncao = $coFuncao;
    }

    /**
     * @return string
     */
    public function getNoFuncao()
    {
        return $this->noFuncao;
    }

    /**
     * @param string $noFuncao
     */
    public function setNoFuncao($noFuncao)
    {
        $this->noFuncao = $noFuncao;
    }

    /**
     * @return string
     */
    public function getDsFuncao()
    {
        return $this->dsFuncao;
    }

    /**
     * @param string $dsFuncao
     */
    public function setDsFuncao($dsFuncao)
    {
        $this->dsFuncao = $dsFuncao;
    }


}

