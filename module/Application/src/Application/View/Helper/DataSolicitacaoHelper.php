<?php


namespace Application\View\Helper;

use Application\Entity\TbSnrProcesso;
use Application\Service\HistEtapaSituacaoService;
use Symfony\Component\Debug\Debug;
use Zend\Di\ServiceLocator;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class DataSolicitacaoHelper extends AbstractHelper
{
    protected $request;
    protected $sm;

    public function __construct(Request $request, ServiceManager $sm)
    {
        $this->request = $request;
        $this->sm = $sm;
    }

    public function getDataSolicitacao(TbSnrProcesso $entProcesso, $coEtapa = null, $coSituacaoProcesso = null)
    {
        /** @var HistEtapaSituacaoService $srvHistEtapaSituacao */
        $srvHistEtapaSituacao = $this->sm->get(HIST_ETAPA_SITUACAO_SERVICE);

        $historico =
            $srvHistEtapaSituacao->findDataSolicitacaoByProcessoStatus($entProcesso, $coEtapa, $coSituacaoProcesso);

        if (!$historico) {
            $dtSolicitacao = "";
        } else {
            $dtSolicitacao = $historico->getDtAlteracao()->format(D_M_Y);
        }

        return $dtSolicitacao;
    }

}
