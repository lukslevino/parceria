<?php
/**
 * Created by PhpStorm.
 * User: lazevedol
 * Date: 20/11/2017
 * Time: 23:42
 */

namespace Application\Controller;


use Application\Service\UsuarioService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UsuarioController extends AbstractActionController
{
    public function solicitarAcessoAction()
    {
        /** @var UsuarioService $usuarioService */
        $usuarioService = $this->getServiceLocator()->get('usuarioService');

        $view = new ViewModel();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $view->setTerminal(true);
            $view->setTemplate('application/ajax/vazio.phtml');


            $post = $request->getPost();

            if ($post['cpf']){

                $entUsuario = $usuarioService->findOneBy(['nu_cpf'=>$post['cpf']]);

                if (!empty($entUsuario) && $entUsuario->getCoUsuario()){
                    //msg de erro
                    $view->setTerminal(true);
                    $view->setTemplate('application/ajax/vazio.phtml');
                    return $view;
                }
            }
        }

        return $view;

    }

    public function acessoAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('/application/usuario/acesso.phtml');
        return $viewModel;
    }
}