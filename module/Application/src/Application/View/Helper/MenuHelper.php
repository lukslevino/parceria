<?php


namespace Application\View\Helper;

use Symfony\Component\Debug\Debug;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class MenuHelper extends AbstractHelper
{

    protected $request;
    protected $sm;
    protected $funcionalidades;

    public function __construct(Request $request, ServiceManager $sm)
    {
        $this->request = $request;
        $this->sm = $sm;
    }

    public function __invoke($menu = [])
    {
        $adesaoService = $this->sm->getServiceLocator()->get(ADESAO_SERVICE);
        $authService   = $this->sm->getServiceLocator()->get(AUTH_SERVICE);
        $iesService    = $this->sm->getServiceLocator()->get(DM_IES_SERVICE);
        $srvConfig     = $this->sm->getServiceLocator()->get('Config');

        if(isset($authService->getIdentity()->coFuncao) && $authService->getIdentity()->coFuncao == CO_FUNCAO_REITOR){
            $adesaoReavaliacao    = $adesaoService->findLastAdesaoPorIesTipo(
                $authService->getIdentity()->coIes, $srvConfig[ID_PARAMETROS][MODELO_TERMO_ADESAO_REVALIDACAO]);
            $adesaoReconhecimento = $adesaoService->findLastAdesaoPorIesTipo(
                $authService->getIdentity()->coIes, $srvConfig[ID_PARAMETROS][MODELO_TERMO_ADESAO_RECONHECIMENTO]);

            //Caso nao tenha nenhuma adesao mostra apenas os menus de Revalidação e Reconhecimanto
            if (!$adesaoReavaliacao && !$adesaoReconhecimento){
                $menu = $this->funcionalidades;
            }
            if (!$adesaoReavaliacao) {
                $menu[PF_REVALIDACAO] = $this->funcionalidades[PF_REVALIDACAO];
            }
            if (!$adesaoReconhecimento) {
                $menu[PF_RECONHECIMENTO] = $this->funcionalidades[PF_RECONHECIMENTO];
            }
        }

        if(isset($authService->getIdentity()->coIes)){
            $ies = $iesService->find($authService->getIdentity()->coIes);

            if($ies->getCoDmCategoriaAdm()->getDsCategoriaAdmResumida() != PUBLICA){
                unset($menu[PF_REVALIDACAO]);
            }
        }

        return $menu;
    }

}