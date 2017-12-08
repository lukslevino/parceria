<?php

namespace Application\Service;

use Application\Entity\DmaPais;
use Application\Entity\DmIes;
use Application\Entity\DmLocalizacao;
use Application\Entity\DmPais;
use Application\Entity\Repository\TbSnrUsuarioRepository;
use Application\Entity\TbSnrFuncao;
use Application\Entity\TbSnrSituacaoUsuFuncao;
use Application\Entity\TbSnrUsuario;
use Application\Entity\TbSnrUsuarioFuncao;
use Application\Enum\FuncaoEnum;
use Application\Enum\TipoFuncaoEnum;
use Application\Util\MensagemUtil;
use Application\Util\StringUtil;
use Base\Service\AbstractService;
use Zend\Authentication\AuthenticationService;

class UsuarioService extends AbstractService
{

    const USUARIO_SAVE_SUCESS = 15;
    const USUARIO_SAVE_FAIL = 16;
    const DIPLOMADO_SAVE_SUCCESS = 17;
    const DIPLOMADO_SAVE_FAIL = 18;

    /**
     * Busca usuário por id
     * @param integer $id Id da entidade
     */
    public function find($id)
    {
        $em = $this->getRepository();
        return $em->find($id);
    }

    /**
     * @return TbSnrUsuarioRepository
     */
    public function getRepository()
    {
        return $this->getEntityManager()->getRepository('Application\Entity\tb_ol_usuario');
    }

    /**
     * Busca usuários por critérios definidos no filtro de pesquisa
     * @param $post
     * @return array
     */
    public function findAllByPost($post)
    {
        /** @var UsuarioFuncaoService $srvUsuarioFuncao */
        $srvUsuarioFuncao = $this->getServiceManager()->get(USUARIO_FUNCAO_SERVICE);
        /** @var AuthenticationService $srvAuth */
        $srvAuth = $this->getServiceManager()->get(AUTH_SERVICE);

        /** @var TbSnrUsuarioFuncao $entUsuarioFuncao */
        $entUsuarioFuncao = $srvUsuarioFuncao->find($this->getCoUsuarioUltimaOperacao());
        /** @var DmIes $entDmIes */
        $entDmIes = $entUsuarioFuncao->getCoDmIes();

        if ($entDmIes && $entUsuarioFuncao->getCoFuncao()->getCoFuncao() != FuncaoEnum::GESTOR_MEC) {
            /** @var DmIesService $srvDmIes */
            $srvDmIes = $this->getServiceManager()->get(DM_IES_SERVICE);
            $arrEntDmIes = $srvDmIes->findBy([CO_IES => $entDmIes->getCoIes()]);

            $arCoDmIes = [];
            # isso é necessário pois algumas IES têm o mesmo co_ies mas co_dm_ies diferentes
            /** @var DmIes $ies */
            foreach ($arrEntDmIes as $ies) {
                $arCoDmIes[] = $ies->getCoDmIes();
            }

            $post->arCoDmIes = $arCoDmIes;
        }

        $identity = $srvAuth->getIdentity();

        $data = $this->getRepository()->findAllByPost($post, $identity);
        foreach ($data as $key => $valor) {
            $data[$key]['nuCpf'] = StringUtil::mask($valor['nuCpf'], '###.###.###-##');
        }
        return $data;
    }

    /**
     * Recupera informações do usuário logado
     */
    public function getCoUsuarioUltimaOperacao()
    {
        $service = $this->getServiceManager()->get(USUARIO_FUNCAO_SERVICE);
        return $service->getCoUsuarioUltimaOperacao();
    }

    /**
     * Busca usuário por critério
     * @param array $dados Critérios de busca
     */
    public function findBy($dados)
    {
        return $this->getRepository()->findBy($dados);
    }

    /**
     * Busca 1 usuário por critério
     * @param array $dados Critérios de busca
     */
    public function findOneBy($dados)
    {
        return $this->getRepository()->findOneBy($dados);
    }

    /**
     * Valida o usuário
     * @param string $autorizacao Nova situação do usuário
     * @param integer $id Id do usuário
     */
    public function validarUsuario($coUsuarioFuncao, $coFuncao, $dados)
    {
        $retorno = [0 => true];

        $usuarioFuncaoService = $this->getServiceManager()->get(USUARIO_FUNCAO_SERVICE);
        $dadosUsuarioFuncao = $usuarioFuncaoService->find($coUsuarioFuncao);

        if ($this->validaPerfilReitor($coFuncao, $dadosUsuarioFuncao->getCoDmIes(), $coUsuarioFuncao)) {

            $situacaoUsuFuncaoService = $this->getServiceManager()->get(SITUACAO_USU_FUNCAO_SERVICE);
            $coSituacaoUsuFuncao = $situacaoUsuFuncaoService->find($dados->stAutorizacao);

            $stPontoFocal = PONTO_FOCAL_N;
            $tpFuncao = null;

            if ($dados->stPontoFocalRevalidacao && !$dados->stPontoFocalReconhecimento) {
                $stPontoFocal = PONTO_FOCAL_S;
                $tpFuncao = TipoFuncaoEnum::REVALIDACAO;
            } elseif (!$dados->stPontoFocalRevalidacao && $dados->stPontoFocalReconhecimento) {
                $stPontoFocal = PONTO_FOCAL_S;
                $tpFuncao = TipoFuncaoEnum::RECONHECIMENTO;
            } elseif ($dados->stPontoFocalRevalidacao && $dados->stPontoFocalReconhecimento) {
                $stPontoFocal = PONTO_FOCAL_S;
                $tpFuncao = TipoFuncaoEnum::REVALIDACAO_RECONHECIMENTO;
            }

            try {
                $this->getEntityManager()->getConnection()->beginTransaction();
                $usuarioFuncaoService->save(
                    [
                        CO_SITUACAO_USU_FUNCAO => $coSituacaoUsuFuncao,
                        ST_PONTO_FOCAL => $stPontoFocal,
                        TP_FUNCAO => $tpFuncao,
                    ],
                    $coUsuarioFuncao
                );
                if ($tpFuncao) {
                    $usuarioFuncaoService->removerPontosFocaisDaIesPorTipo($dadosUsuarioFuncao, $tpFuncao);
                }

                # salva o novo ponto focal de revalidação, se existir
                if ($dados->pontoFocalRvNovo) {
                    $usuFuncEntityRv = $usuarioFuncaoService->find($dados->pontoFocalRvNovo);
                    $usuarioFuncaoService->adicionarPontoFocal($usuFuncEntityRv, TipoFuncaoEnum::REVALIDACAO);
                }
                # salva o novo ponto focal de reconhecimento, se existir
                if ($dados->pontoFocalRcNovo) {
                    $usuFuncEntityRc = $usuarioFuncaoService->find($dados->pontoFocalRcNovo);
                    $usuarioFuncaoService->adicionarPontoFocal($usuFuncEntityRc, TipoFuncaoEnum::RECONHECIMENTO);
                }
                $this->getEntityManager()->getConnection()->commit();
            } catch (Exception $e) {
                $this->getEntityManager()->getConnection()->rollback();
                $retorno[0] = false;
                $retorno['msg'] = 'Erro ao salvar usuário.';
            }
        } else {
            $retorno[0] = false;
            $retorno['msg'] = MensagemUtil::getMensagem('MSG026');
        }

        return $retorno;
    }

    /**
     * Valida a existência de um usuário com o perfil Reitor
     * RN[1.1.2] -> Condição para acesso de Reitor
     * @param integer $coFuncaoSelecionada Id da função Reitor
     */
    public function validaPerfilReitor($coFuncaoSelecionada, $coDmIes, $coUsuarioFuncao)
    {
        $config = $this->getServiceManager()->get(CONFIG);

        # Verifica se o perfil selecionado é perfil reitor
        if ($coFuncaoSelecionada == FuncaoEnum::REITOR) {

            $idSituacaoUsuarioAtivo = $config['id_situacoes_usuario']['ativo'];

            $usuarioFuncaoService = $this->getServiceManager()->get(USUARIO_FUNCAO_SERVICE);

            $retorno = $usuarioFuncaoService->findBy([
                CO_FUNCAO => FuncaoEnum::REITOR,
                CO_DM_IES => $coDmIes,
                CO_SITUACAO_USU_FUNCAO => $idSituacaoUsuarioAtivo
            ]);

            $ret = null;
            if (count($retorno) > 1) {
                $ret = false;
            } elseif (count($retorno) == 1) {

                if ($retorno[0]->getCoUsuarioFuncao() == $coUsuarioFuncao) {
                    $ret = true;
                } else {
                    $ret = false;
                }

            } else {
                $ret = true;
            }

            return !is_null($ret) ? $ret : empty($retorno);

        }

        return true;
    }

    /**
     * Busca a lista de perfis que podem ser visualizados pelo perfil do usuário logado
     */
    public function getPerfisByPerfilUsuario()
    {
        $service = $this->getServiceManager()->get('funcaoService');
        return $service->getPerfisByPerfilUsuario();
    }

    /**
     * Lista os pacotes de funcionalidades
     */
    public function getPacotes()
    {
        $service = $this->getServiceManager()->get(PACOTE_SERVICE);
        return $service->findList();
    }

    /**
     * Lista os usuários por pacote de funcionalidade
     */
    public function findUserPacotesCadastrados($id = null)
    {
        $service = $this->getServiceManager()->get(USUARIO_FUNC_PAC_FUNC_SERVICE);
        return $service->findUserPacotesCadastrados($id);
    }

    /**
     * Lista as possiveis situações de um usuário x função
     */
    public function getSituacoesUsuario()
    {
        $situacaoUsuFuncaoService = $this->getServiceManager()->get(SITUACAO_USU_FUNCAO_SERVICE);
        return $situacaoUsuFuncaoService->findListAll();
    }

    /**
     * Obtém a IES vinculada ao usuário x função
     */
    public function getIesUsuarioFuncao($coUsuarioFuncao)
    {

        $ufService = $this->getServiceManager()->get(USUARIO_FUNCAO_SERVICE);
        $dmIesService = $this->getServiceManager()->get(DM_IES_SERVICE);

        $dadosUsuarioFuncao = $ufService->findOneBy([CO_USUARIO_FUNCAO => $coUsuarioFuncao]);
        $coDmIesUsuarioFuncao = $dadosUsuarioFuncao->getCoDmIes();

        if (!empty($coDmIesUsuarioFuncao)) {
            $dadosIes = $dmIesService->findOneBy([CO_DM_IES => $coDmIesUsuarioFuncao]);
            return $dadosIes->getNoIes();
        }

        return '';
    }

    /**
     * Inclui um usuário no sistema que veio do SSD
     * e ainda não possui registro na base do Revalidação
     * @param object $userSsd
     */
    public function saveUsuarioSsd($userSsd)
    {
        try {
            return $this->getRepository()->saveSsd($userSsd);
        } catch (\Exception $e) {
            throw new $e;
        }
    }

    /**
     * Inclui um usuário sem CPF (Estrangeiro)
     * @param array $dados
     */
    public function saveUsuarioEstrangeiro($dados)
    {
        $utilityService = $this->getServiceManager()->get('utilityService');
        $input = $utilityService->paramsCompose($this, __FUNCTION__, func_get_args());

        $usuarioFuncPacFuncService = $this->getServiceManager()->get(USUARIO_FUNC_PAC_FUNC_SERVICE);

        $funcaoService = $this->getServiceManager()->get(FUNCAO_SERVICE);
        $sitUsuFuncService = $this->getServiceManager()->get(SITUACAO_USU_FUNCAO_SERVICE);
        $usuarioFuncaoService = $this->getServiceManager()->get(USUARIO_FUNCAO_SERVICE);
        $pacoteService = $this->getServiceManager()->get(PACOTE_SERVICE);
        $diplomadoService = $this->getServiceManager()->get(DIPLOMADO_SERVICE);
        $contatoService = $this->getServiceManager()->get(CONTATO_SERVICE);
        $dmLocalizacaoService = $this->getServiceManager()->get(LOCALIZACAO_SERVICE);

        try {
            $this->getEntityManager()->getConnection()->beginTransaction();

            $dtNow = new \DateTime(date(Y_M_D_H_I_S));

            //Usuário
            $dtValidadeRne = !empty($dados[DT_VALIDADE_RNE]) ? \DateTime::createFromFormat(D_M_Y,
                $dados[DT_VALIDADE_RNE]) : null;

            $arUsuario = [];
            $arUsuario[NO_USUARIO] = mb_strtoupper($dados[NO_USUARIO]);
            $arUsuario[DS_EMAIL] = $dados[DS_EMAIL];
            $arUsuario[DT_INCLUSAO] = $dtNow;
            $arUsuario[NU_RNE] = $dados[NU_RNE];
            $arUsuario[DT_VALIDADE_RNE] = $dtValidadeRne;
            $arUsuario[DS_SENHA] = sha1($dados[DS_SENHA] . SALT_PASS);
            $arUsuario[DT_ULTIMO_ACESSO] = $dtNow;

            $usuario = $this->save($arUsuario);

            $entCoUsuario = $this->getEntityManager()->getReference(TbSnrUsuario::class, $usuario->getCoUsuario());

            //Função do Usuário - (Diplomado)
            $coFuncaoDiplomado = $funcaoService->findOneByOne([NO_FUNCAO => FUNCAO_DIPLOMADO]);
            $coSituacaoUsuFunc = $sitUsuFuncService->findOneBy([NO_SITUACAO_USU_FUNCAO => SITUACAO_ATIVO]);

            $entCoFuncaoDiplomado = $this->getEntityManager()->getReference(TbSnrFuncao::class,
                $coFuncaoDiplomado->getCoFuncao());
            $entCoSituacaoUsuFunc = $this->getEntityManager()->getReference(TbSnrSituacaoUsuFuncao::class,
                $coSituacaoUsuFunc->getCoSituacaoUsuFuncao());

            $arUsuarioFuncao = [];
            $arUsuarioFuncao[CO_USUARIO] = $entCoUsuario;
            $arUsuarioFuncao[CO_FUNCAO] = $entCoFuncaoDiplomado;
            $arUsuarioFuncao[ST_AUTORIZACAO] = ST_AUTORIZACAO_ATIVO;
            $arUsuarioFuncao[DT_AUTORIZACAO] = $dtNow;
            $arUsuarioFuncao[DT_INCLUSAO] = $dtNow;
            $arUsuarioFuncao[CO_SITUACAO_USU_FUNCAO] = $entCoSituacaoUsuFunc;

            $usuarioFuncao = $usuarioFuncaoService->incluirUsuarioFuncao($arUsuarioFuncao);

            $entCoUsuarioFuncao = $this->getEntityManager()->getReference(TbSnrUsuarioFuncao::class,
                $usuarioFuncao->getCoUsuarioFuncao());

            // Função Usuário Pacote - (Diplomado)
            $coPacoteDiplomado = $pacoteService->findOneByOne([NO_PACOTE => PACOTE_DIPLOMADO]);
            $usuarioFuncPacFuncService->addFuncionalidadesByPacote($coPacoteDiplomado->getCoPacote(),
                $entCoUsuarioFuncao->getCoUsuarioFuncao());

            //Diplomado
            $dtNascimento = \DateTime::createFromFormat(D_M_Y, $dados[DT_NASCIMENTO]);

            if (!empty($dados[CO_MUNICIPIO])) {
                $localizacao = $dmLocalizacaoService->find($dados[CO_MUNICIPIO]);
            } else {
                $localizacao = $this->getEntityManager()->getReference(DmLocalizacao::class, MUNICIPIO_DF);
            }

            $arDiplomado = [];
            $arDiplomado[CO_USUARIO_FUNCAO] = $entCoUsuarioFuncao;
            $arDiplomado[CO_MUNICIPIO] = $localizacao;
            $arDiplomado[DT_NASCIMENTO] = $dtNascimento;
            $arDiplomado[DS_LOGRADOURO] = $dados[DS_LOGRADOURO];
            $arDiplomado[NU_CEP] = StringUtil::removeMascara($dados[NU_CEP]);
            $arDiplomado[DT_INCLUSAO] = $dtNow;
            $arDiplomado[TP_SEXO] = mb_strtoupper($dados[TP_SEXO]);
            $arDiplomado[NU_ENDERECO] = $dados[NU_ENDERECO];
            $arDiplomado[DS_COMPLEMENTO_ENDERECO] = $dados[DS_COMPLEMENTO_ENDERECO];
            $arDiplomado[NO_BAIRRO] = $dados[NO_BAIRRO];
            $arDiplomado[ST_ENDERECO_ESTRANGEIRO] = $dados[ST_ENDERECO_ESTRANGEIRO];
            $arDiplomado[CO_NACIONALIDADE] = $this->getEntityManager()->getReference(DmaPais::class,
                $dados[CO_PAIS_NASCIMENTO]);
            $arDiplomado[CO_DM_PAIS_NASCIMENTO] = $this->getEntityManager()->getReference(DmPais::class,
                $dados[CO_PAIS_NASCIMENTO]);
            $arDiplomado[CO_DM_PAIS_CIDADANIA] = $this->getEntityManager()->getReference(DmPais::class,
                $dados[CO_PAIS_CIDADANIA]);
            $arDiplomado[CO_USUARIO_ULTIMA_OPERACAO] = $entCoUsuarioFuncao;

            $arDiplomado[DS_NATURALIDADE] = ESTRANGEIRA;

            $diplomadoService->save($arDiplomado);

            //Contado do usuário - (Telefones)
            $arTelefones = [$dados[NU_TELEFONE], $dados[NU_CELULAR]];

            foreach ($arTelefones as $nuTelefone) {

                if (!empty($nuTelefone)) {
                    $arContato[CO_USUARIO_FUNCAO] = $this->getEntityManager()->getReference(TbSnrUsuarioFuncao::class,
                        $usuarioFuncao->getCoUsuarioFuncao());
                    $arContato[TP_CONTATO] = UsuarioContatoService::TP_TELEFONE;
                    $arContato[DS_CONTATO] = StringUtil::removeMascara($nuTelefone);
                    $arContato[DT_CARGA_DW] = $dtNow;
                    $arContato[DT_INCLUSAO] = $dtNow;
                    $arContato[CO_USUARIO_ULTIMA_OPERACAO] =
                        $this->getEntityManager()
                            ->getReference(TbSnrUsuarioFuncao::class, $usuarioFuncao->getCoUsuarioFuncao());

                    $contatoService->save($arContato);
                }
            }

            $this->getEntityManager()->getConnection()->commit();

            return $usuario;

        } catch (Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            $input['context'] = static::USUARIO_SAVE_FAIL;
            return false;
        }

    }

    /**
     * Inclui ou Atualiza um usuário
     * @param array $dados
     * @param string $id
     */
    public function save($dados, $id = null)
    {
        return $this->getRepository()->save($dados, $id);
    }

    /**
     * Lista todos os usuários da Instituição com perfis Reitor, Gestor Instituição e Usuário Instituição
     * exceto o próprio usuário sendo editado
     * @param DmIes $coDmIes
     */
    public function listarUsuariosPorIes($coDmIes, $coUsuario)
    {
        return $this->getRepository()->listarUsuariosPorIes($coDmIes, $coUsuario);
    }

    public function verificaConvitePendente($identity, $usuarioFuncao)
    {
        # SERVICES
        /** @var UnidadeService $srvUnidade */
        $srvUnidade = $this->getServiceManager()->get(UNIDADE_SERVICE);
        /** @var IntegranteComissaoService $srvIntegranteComissao */
        $srvIntegranteComissao = $this->getServiceManager()->get(INTEGRANTE_COMISSAO_SERVICE);
        /** @var ConselhoService $srvConselho */
        $srvConselho = $this->getServiceManager()->get(CONSELHO_SERVICE);
        /** @var IntegranteConselhoService $srvIntegranteConselho */
        $srvIntegranteConselho = $this->getServiceManager()->get(INTEGRANTE_CONSELHO_SERVICE);
        /** @var RelatorProcessoService $srvRelatorProcesso */
        $srvRelatorProcesso = $this->getServiceManager()->get(RELATOR_PROCESSO_SERVICE);

        $conviteUnidade = $srvUnidade->verificarConvitePendente($usuarioFuncao);
        if (!empty($conviteUnidade)) {
            $identity->conviteRespPendente = true;
        }

        $conviteComissao = $srvIntegranteComissao->verificarConvitePendente($usuarioFuncao);
        if (!empty($conviteComissao)) {
            $identity->conviteMembroComissao = true;
        }

        $conviteConselho = $srvConselho->verificarConvitePendente($usuarioFuncao);
        if (!empty($conviteConselho)) {
            $identity->conviteRepConselhoPendente = true;
        }

        $conviteIntegranteConselho = $srvIntegranteConselho->verificarConvitePendente($usuarioFuncao);
        if (!empty($conviteIntegranteConselho)) {
            $identity->conviteIntegranteConselhoPendente = true;
        }

        $conviteRelatorProcesso = $srvRelatorProcesso->verificarConvitePendente($usuarioFuncao);
        if (!empty($conviteRelatorProcesso)) {
            $identity->conviteRelatorProcessoPendente = true;
        }
    }


    public function saveContato($telefones, $usuarioFuncao)
    {
        /** @var ContatoService $srvUsuarioContato */
        $srvUsuarioContato = $this->getServiceManager()->get(USUARIO_CONTATO_SERVICE);
        $c = [];
        $this->getEntityManager()->getConnection()->beginTransaction();
        try {

            $contatos = $srvUsuarioContato->findBy([CO_USUARIO_FUNCAO => $usuarioFuncao]);
            $entUsuarioFuncao = $this->getEntityManager()->getReference(TbSnrUsuarioFuncao::class, $usuarioFuncao);
            if (count($contatos)) {
                foreach ($contatos as $contato) {
                    if (!in_array($contato->getDsContato(), $telefones)) {
                        $srvUsuarioContato->deleteContato($contato->getCoUsuarioContato());
                    }
                }
                foreach ($telefones as $telefone) {
                    foreach ($contatos as $contato) {
                        $c[] = $contato->getDsContato();
                    }
                    if (!in_array($telefone, $c)) {

                        $dtNow = new \DateTime(date(Y_M_D_H_I_S));
                        $arContato[CO_USUARIO_FUNCAO] = $entUsuarioFuncao;
                        $arContato[TP_CONTATO] = UsuarioContatoService::TP_TELEFONE;
                        $arContato[DS_CONTATO] = $telefone;
                        $arContato[DT_CARGA_DW] = $dtNow;
                        $arContato[DT_INCLUSAO] = $dtNow;
                        $arContato[CO_USUARIO_ULTIMA_OPERACAO] = $entUsuarioFuncao;

                        $srvUsuarioContato->save($arContato);
                        $this->getEntityManager()->getConnection()->commit();
                    }
                }
                return true;
            } else {
                foreach ($telefones as $telefone) {

                    $dtNow = new \DateTime(date(Y_M_D_H_I_S));
                    $arContato[CO_USUARIO_FUNCAO] = $entUsuarioFuncao;
                    $arContato[TP_CONTATO] = UsuarioContatoService::TP_TELEFONE;
                    $arContato[DS_CONTATO] = $telefone;
                    $arContato[DT_CARGA_DW] = $dtNow;
                    $arContato[DT_INCLUSAO] = $dtNow;
                    $arContato[CO_USUARIO_ULTIMA_OPERACAO] = $entUsuarioFuncao;

                    $srvUsuarioContato->save($arContato);
                    $this->getEntityManager()->getConnection()->commit();

                }
                return true;
            }
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            return false;
        }
    }
}
