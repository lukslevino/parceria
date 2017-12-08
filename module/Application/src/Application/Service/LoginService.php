<?php

namespace Application\Service;

use Application\Service\AbstractService;
use Doctrine\Common\Util\Debug;


class LoginService extends AbstractService
{

    public function getRepository()
    {
        return $this->getEntityManager()->getRepository('Application\Entity\UsuarioEntity');
    }

    public function verificaUsuario($cpf)
    {

        $usuario = $this->getRepository()->findOneByNuRne($cpf);

        return (count($usuario) > 0);

    }


    public function validaLogin($cpf, $senha)
    {

        $usuario = $this->getRepository()->findOneByNuRne($cpf);

        if ($usuario && $usuario->getDsSenha() === sha1($senha . 'OrthoLifeParceria')) {

            $srvUsuario = $this->getServiceManager()->get('usuarioService');
            $srvDiplomado = $this->getServiceManager()->get('diplomadoService');
            $srvFuncao = $this->getServiceManager()->get('funcaoService');
            $srvUsuarioFuncao = $this->getServiceManager()->get('usuarioFuncaoService');
            $srvAuth = $this->getServiceManager()->get('authService');


            $contatos = $usuario->getCoUsuarioFuncao()->current()->getCoUsuarioContato();

            $telefones = [];
            foreach ($contatos as $contato) {
                $telefones[] = $contato->getDsContato();
            }


            /**
             * Dados do Usuário
             */
            $identity = new \stdClass();
            $identity->noUsuario = $usuario->getNoUsuario();
            $identity->email = $usuario->getDsEmail();
            $identity->cpf = $usuario->getNuCpf();
            $identity->coUsuario = $usuario->getCoUsuario();
            $identity->noPerfil = "";
            $dtUltimoAcesso = $usuario->getDtUltimoAcesso();
            $identity->dtUltimoAcesso = $dtUltimoAcesso->format('d/m/Y H:i:s');
            $identity->telefones = $telefones;

            /**
             * Busca os dados do Diplomado
             */
            $funcao = $srvFuncao->findOneByOne([
                'noFuncao' => FUNCAO_DIPLOMADO
            ]);

            $usuarioFuncao = $srvUsuarioFuncao->findOneBy([
                'coUsuario' => $usuario->getCoUsuario(),
                CO_FUNCAO => $funcao->getCoFuncao()
            ]);

            $diplomado = $srvDiplomado->findOneBy([
                'coUsuarioFuncao' => $usuarioFuncao->getCoUsuarioFuncao()
            ]);

            $identity->dadosAdicionais = [
                'coNacionalidade' => $diplomado->getCoDmPaisNascimento()->getCoDmPais(),
                'coMunicipio' => $diplomado->getCoMunicipio() ? $diplomado->getCoMunicipio()->getCoDmLocalizacao() : '',
                'dtNascimento' => $diplomado->getDtNascimento()->format('Y-m-d'),
                DS_LOGRADOURO => $diplomado->getDsLogradouro(),
                'nuCep' => $diplomado->getNuCep(),
                'dsNaturalidade' => $diplomado->getDsNaturalidade(),
                'tpSexo' => $diplomado->getTpSexo()
            ];

            /**
             * Salva a data do Ultimo acesso
             */
            $srvUsuario->save(array(
                'dtUltimoAcesso' => new \DateTime(date(Y_M_D_H_I_S))
            ), $usuario->getCoUsuario());


            /**
             * Cria a seção do usuário
             */
            $srvAuth->getStorage()->write($identity);

            return true;

        }

        return false;
    }

    public function recuperarSenha($cpf)
    {
        $usuario = $this->getRepository()->findOneByNuRne($cpf);

        if ($usuario) {

            try {
                //Salva uma nova senha para o usuário
                $newPass = substr(sha1(mt_rand()), 17, 6);
                $novaSenha = sha1($newPass . SALT_PASS);

                $usuario->setDsSenha($novaSenha);

                $this->getRepository()->save($usuario->toArray(), $usuario->getCoUsuario());

                /**
                 * Gera o Link da aplicação
                 */
                $request = $this->getServiceManager()->get('Request');
                $uri = $request->getUri();
                $scheme = $uri->getScheme();
                $host = $uri->getHost();
                $url = sprintf('%s://%s', $scheme, $host);

                //Envia um email para o usuário contendo a nova senha gerada
                $attributesEmail = array(
                    'new_pass' => $newPass,
                    'linkCarolinaBori' => $url
                );
                $mailService = $this->getServiceManager()->get('mailService');
                $mailService->sendMail(
                    $usuario->getDsEmail(),
                    'Nova senha Plataforma Carolina Bori',
                    'atualizacao-senha-rne',
                    $attributesEmail
                );

                return true;

            } catch (\Exception $e) {
                return false;
            }

        }

    }

    public function getUsuarioLogado()
    {
        $authService = $this->getServiceManager()->get('authService');
        if ($authService->hasIdentity()) {
            return $authService->getIdentity();
        }
    }

}
