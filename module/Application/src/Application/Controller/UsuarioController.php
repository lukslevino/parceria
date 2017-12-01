<?php
/**
 * Created by PhpStorm.
 * User: lazevedol
 * Date: 20/11/2017
 * Time: 23:42
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UsuarioController extends AbstractActionController
{
    public function solicitarAcessoAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()){
        $post = $request->getPost();
        }

        $view = new ViewModel();
        $view->setTerminal(false);
        $view->setTemplate('application/ajax/vazio');
        print_r("Controller1");
        return $view;

    }
    public function acessoAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('/application/usuario/acesso.phtml');
        return $viewModel;
    }
}