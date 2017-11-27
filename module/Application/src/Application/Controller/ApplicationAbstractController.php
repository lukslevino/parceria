<?php

namespace Application\Controller;

use Application\Entity\ComissaoUsuarioEntity;
use Application\Enum\ComissaoEnum;
use Application\Enum\SimNaoEnum;
use Application\Util\MensagemUtil;
use Application\Util\SessionUtil;
use Base\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * Class ApplicationAbstractController
 * @package Application\Controller
 */
class ApplicationAbstractController extends AbstractActionController
{
    const BUSCA_AUTOMATICA = 'buscaAutomatica';

    public function getService()
    {
        $fullClassName = get_class($this);
        $fullServiceClassName = str_replace("Controller", "Service", $fullClassName);
        $lastPositionPackge = strrpos($fullServiceClassName, "\\");
        $serviceClassName = substr($fullServiceClassName, $lastPositionPackge + 1);
        $lowerFirstServiceClassName = lcfirst($serviceClassName);

        return $this->getServiceLocator()
            ->get($lowerFirstServiceClassName);
    }

    public function getConfig()
    {
        return new \Zend\Config\Config(include getcwd() . DIRECTORY_SEPARATOR . CONFIG . DIRECTORY_SEPARATOR .
            AUTOLOAD . DIRECTORY_SEPARATOR . 'property.global.php');
    }

    public function getConfigArray()
    {
        $property = new \Zend\Config\Config(
            include getcwd() . DIRECTORY_SEPARATOR . CONFIG . DIRECTORY_SEPARATOR .
                AUTOLOAD . DIRECTORY_SEPARATOR . 'property.global.php'
        );
        $resourcePrivate = new \Zend\Config\Config(
            include getcwd() . DIRECTORY_SEPARATOR . CONFIG . DIRECTORY_SEPARATOR .
                AUTOLOAD . DIRECTORY_SEPARATOR . 'resource.private.global.php'
        );
        $appConfigFile = new \Zend\Config\Config(include getenv('APP_CONFIG_FILE'));
        $arrayFinal = array_merge($property->toArray(), $appConfigFile->toArray());
        return array_merge($arrayFinal, $resourcePrivate->toArray());
    }

    /**
     * Recuperar Serviço de Autenticação
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        return new AuthenticationService();
    }

    /**
     * Recuperar os dados do usuário logado
     * @return mixed|null
     */
    public function getUser()
    {
        return $this->getAuthService()->getIdentity();
    }

    public function getMessageRenderAction()
    {
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate(AJAX_VAZIO);

        $request = $this->getRequest();
        $post = $request->getPost();

        if (isset($post['messageSubs'])) {
            foreach ($post['messageCod'] as $key => $cod) {
                $this->addMessage(MensagemUtil::get($cod, $post['messageSubs'][$key]), $post['messageType'][$key]);
            }
        } else {
            foreach ($post['messageCod'] as $key => $cod) {
                $this->addMessage(MensagemUtil::get($cod), $post['messageType'][$key]);
            }
        }

        return $view;
    }

    public function orderArrayToJson($array)
    {
        $resultJson = array();
        foreach ($array as $key => $value) {
            array_push($resultJson, array($key => $value));
        }
        return $resultJson;
    }

    /**
     * Função que alterna o modo de visualização do sistema e retorna se o usuário logado é CNRM ou CNRMS
     *
     * @param ViewModel $view
     * @return int|null
     */
    public function getLayoutAutenticacao(&$view)
    {
        $srvComissaoUsuario = $this->getServiceLocator()->get(COMISSAO_USUARIO_SERVICE);
        $retorno = null;
        $layout = false;
        $usuario = $this->getUser();
        /** @var ComissaoUsuarioEntity $comisaoUsuario */
        $comisaoUsuario = null;
        if (!empty($usuario->coComissaoUsuario)) {
            $comisaoUsuario = $srvComissaoUsuario->find($usuario->coComissaoUsuario);
        } else {
            $comisaoUsuario = $srvComissaoUsuario->findOneBy(array(
                CO_USUARIO => $usuario->coUsuario,
                ST_ATIVO => SimNaoEnum::SIM
            ));
        }

        if (ComissaoEnum::CNRMS === (int)$comisaoUsuario->getCoComissao()->getCoComissao()) {
            $layout = true;
            $retorno = ComissaoEnum::CNRMS;
        } else {
            $retorno = $comisaoUsuario->getCoComissao()->getCoComissao();
        }

        $view->setVariable('cnrmsHead', $layout);
        return $retorno;
    }

    /**
     * Função que registra na seção o filtro da pesquisa
     *
     * @param Request $request
     */
    public function registraFiltro($request = null, $name = APPLICATION_SESSION)
    {
        if (empty($request)) {
            $request = $this->getServiceLocator()->get('Request');
        }

        if ($request->isPost()) {
            SessionUtil::registraFiltro($request, $name);
        }
    }

    /**
     * Função que restaura o filtro previamente registrado em seção
     *
     * @param ViewModel $view
     * @param Form $form
     */
    public function restauraFiltro(&$view, &$form, $name = APPLICATION_SESSION, $limpaFiltro = false)
    {
        $view->setVariable(
            static::BUSCA_AUTOMATICA,
            SessionUtil::restauraFiltro($form, $name, $this->getRequest(), $limpaFiltro)
        );
    }

    public function exportar(&$view, $response, $data, $exportTo, $path, $name)
    {
        $exportService = $this->getServiceLocator()->get('ExportService');
        return $exportService->exportar($view, $response, $data, $exportTo, $path, $name);
    }

    /**
     * Verifica se a requisiçao contem o parametro voltar e se existe sessao aberta
     * para restauraçao dos valores do form
     * @return bool
     */
    protected function verificarBotaoVoltar($nameSession = APPLICATION_SESSION)
    {
        if ($this->verificarUrlVoltar() && SessionUtil::checkSessionExists($nameSession)) {
            return true;
        }
        return false;
    }

    /**
     * Verifica se existe o parametro voltar na url
     * @return bool
     */
    protected function verificarUrlVoltar()
    {
        /* @var $request \Zend\Http\Request */
        $request = $this->getServiceLocator()->get('Request');
        if ($request->getQuery('voltar')) {
            return true;
        }
        return false;
    }

    /**
     * Retorna o post da requisiçao ou da sessao
     * @param string $name
     * @return mixed|\Zend\Stdlib\ParametersInterface
     */
    protected function getPost($name = APPLICATION_SESSION)
    {
        /* @var $request \Zend\Http\Request */
        $request = $this->getServiceLocator()->get('Request');

        if ($request->isPost()) {
            $this->registraFiltro($request, $name);
            return $request->getPost();
        }
        if ($this->verificarBotaoVoltar($name)) {
            $container = new Container($name);
            if ((isset($container->post)) && ($container->post != null) && (!empty($container->post))) {
                return $container->post;
            }
        }
        return $request->getPost();
    }

    /**
     * Método responsável por voltar os dados
     * @param $view
     * @param $form
     * @param string $name
     */
    public function voltarDados(&$view, &$form, $name = APPLICATION_SESSION)
    {
        if ($this->verificarBotaoVoltar($name)) {
            return $this->restauraFiltro($view, $form, $name);
        }
        $view->setVariable(static::BUSCA_AUTOMATICA, false);
    }

    public function isPost($name = APPLICATION_SESSION)
    {
        $request = $this->getServiceLocator()->get('Request');
        return $request->isPost() || $this->verificarBotaoVoltar($name);
    }

    /**
     * Limpar Dados
     * @param $view
     */
    public function limparDados(&$view)
    {
        $request = $this->getServiceLocator()->get('Request');
        $limpar = $request->getQuery('limpar', null);
        if ($limpar) {
            $view->setVariable(static::BUSCA_AUTOMATICA, null);
        }
    }


    public function addMessage($message, $type = null)
    {
        if (is_array($message)) {
            foreach ($message as $msg) {
                $this->addMessage($msg, $type);
            }
        } else {
            if (!$type) {
                $type = $message[0];
            }
            switch ($type) {
                case 'I':
                    return $this->addInfoMessage($message);
                case 'S':
                    return $this->addSuccessMessage($message);
                case 'W':
                    return $this->addWarningMessage($message);
                default:
                    return $this->addErrorMessage($message);
            }
        }
    }

    /**
     * Add a message with "Warning" type
     *
     * @param  string $message
     * @return FlashMessenger
     */
    public function addWarningMessage($message)
    {
        return $this->flashMessenger()->addWarningMessage($this->translate($message));
    }
}
