<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Service\LoginService;
use Application\Util\MensagemUtil;
use Application\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {

        $sm = $this->getServiceLocator();
        $srvAuth = $sm->get('authService');
        /** @var LoginService $loginService */
        $loginService = $sm->get('loginService');
        if (!$srvAuth->hasIdentity()) {
            return $this->redirect()->toUrl('/usuario/acesso');
        }

        return new ViewModel();
    }

    public function acessoAction()
    {
        $view = new ViewModel();
        $request = $this->getRequest();
        if ($request->isPost()){
            $post = $request->getPost();
            /** @var LoginService $loginService */
            $loginService = $this->getServiceLocator()->get('loginService');
            dumpd($loginService);
            if (!$loginService->verificaUsuario($post['senhaUsuario'])){

                $type = $post('recuperarSenha') !== '1'?'usuario-nao-encontrado' : 'cpf-nao-encontrado';

                $mensagem = $post('recuperarSenha') !== '1' ?'Usuário não encontrado! Deseja se Cadastrar?':
                    'E-mail não enviado. Nº CPF não localizado. Deseja se cadastrar?';
                return $this->sendJson($type, $mensagem);
            }elseif ($post('recuperarSenha') === '1') {
                $result = $loginService->recuperarSenha($post['cpf']);
                $mensagem = $result ? 'A sua nova senha foi enviada para o e-mail cadastrado.':
                    'Erro ao gerar uma nova Senha. Tente Novamente.';
                $this->addErrorMessage($mensagem);
            } elseif ($loginService->validaLogin(
                $post['cpf'],
                $post['senhaUsuario'])
            ) {

            $view->setVariable('redirect', '/');
        } else {
                $mensagem = 'Senha inválida.';
               $this->addErrorMessage($mensagem);
            }

            $view->setTerminal(true);
            $view->setTemplate('application/ajax/vazio.phtml');

        }

        return $view;

    }
    /**
     * Sair do sistema e limpar todas as sessões relacionadas ao usuário
     * @return \Zend\Http\Response
     */
    public function sairAction()
    {
        $authService = $this->getServiceLocator()->get('authService');
        $authService->clearIdentity();

        return $this->redirect()->toRoute('home');
    }

}
