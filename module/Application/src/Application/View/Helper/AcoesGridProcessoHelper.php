<?php

namespace Application\View\Helper;

use Application\Entity\TbPcbConselho;
use Application\Entity\TbPcbIntegranteComissao;
use Application\Entity\TbPcbRelatorProcesso;
use Application\Entity\TbPcbUnidade;
use Application\Enum\FuncaoEnum;
use Application\Enum\SimNaoEnum;
use Application\Enum\SituacaoProcessoEnum;
use Application\Enum\StatusConviteEnum;
use Application\Enum\TipoFuncaoEnum;
use Application\Enum\TipoUnidadeEnum;
use Application\Service\ConselhoService;
use Application\Service\IntegranteComissaoService;
use Application\Service\RecursoService;
use Application\Service\RelatorProcessoService;
use Application\Service\UnidadeService;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Application\Enum\EtapaEnum;
use Application\Enum\IesEstrangeiraEnum;

class AcoesGridProcessoHelper extends AbstractHelper
{
    protected $request;

    protected $sm;

    const DETALHAR_PROCESSO = 'Detalhar Processo';

    const FA_FA_EYE = 'fa fa-eye';

    const FA_FA_LIST = 'fa fa-list';

    public function __construct(Request $request, ServiceManager $sm)
    {
        $this->request = $request;
        $this->sm = $sm;
    }

    /**
     * @param $processo
     * @return string
     */
    public function buscarAcoesGerenciarProcesso($processo)
    {
        /** @var UnidadeService $srvUnidade */
        $srvUnidade = $this->sm->get(UNIDADE_SERVICE);
        /** @var AuthenticationService $srvAuth */
        $srvAuth = $this->sm->get(AUTH_SERVICE);
        /** @var IntegranteComissaoService $srvIntegranteComissao */
        $srvIntegranteComissao = $this->sm->get(INTEGRANTE_COMISSAO_SERVICE);
        /** @var ConselhoService $srvConselho */
        $srvConselho = $this->sm->get(CONSELHO_SERVICE);
        /** @var RelatorProcessoService $srvRelatorProcesso */
        $srvRelatorProcesso = $this->sm->get(RELATOR_PROCESSO_SERVICE);

        $identity = $srvAuth->getIdentity();

        /** @var TbPcbUnidade $entGestorResponsavel */
        $entGestorResponsavel = $srvUnidade->findOneBy([
            CO_IES => $identity->coEmec,
            CO_USU_FUNC_REPRESENTANTE => $identity->coUsuarioFuncao,
            ST_CONVITE_REPRESENTANTE => StatusConviteEnum::ACEITO,
            CO_TIPO_UNIDADE => TipoUnidadeEnum::GESTOR_RESPONSAVEL,
            TP_PROCEDIMENTO => TipoFuncaoEnum::retornarTpProcedimentoPorTipoSolicitacao(
                $processo[CO_TIPO_SOLICITACAO]
            )
        ]);

        $coComissao = $processo[CO_COMISSAO];
        $coProcesso = $processo[CO_PROCESSO];
        $coSituacaoProcesso = $processo[CO_SITUACAO_PROCESSO];

        $acoes =
            "<a href=\"/processo/detalharProcesso/$coProcesso\">"
            . $this->montaIcone(static::FA_FA_EYE, static::DETALHAR_PROCESSO) .
            "</a>";

        $arSituacaoCancelada = [
            SituacaoProcessoEnum::CANCELADA_IES,
            SituacaoProcessoEnum::CANCELADA,
            SituacaoProcessoEnum::EXCLUIDA

        ];
        if (!in_array($coSituacaoProcesso, $arSituacaoCancelada)){

        $acoes .=
            "<a href=\"#\" onclick=\"cancelaProcesso($coProcesso);\">"
            . $this->montaIcone('fa fa-minus-circle', 'Cancelar Processo') .
            "</a>";
        }

        if (!empty($entGestorResponsavel)
            && $coSituacaoProcesso == SituacaoProcessoEnum::AGUARDANDO_VALIDACAO_TRAM_SIMPLIFICADA) {
            $acoes .=
                "<a href=\"/processo/validar-tramitacao-simplificada/$coProcesso\">"
                . $this->montaIcone('fa fa-check-square-o', 'Validar Tramitação Simplificada') .
                "</a>";
        }

        $arSituacoesDistribuir = [
            SituacaoProcessoEnum::ENCAMINHADO_GESTOR_RESPONSAVEL,
            SituacaoProcessoEnum::ENCAMINHADO_FACULDADES,
            SituacaoProcessoEnum::ENCAMINHADO_CURSOS,
        ];

        if (in_array($coSituacaoProcesso, $arSituacoesDistribuir)
            && !$coComissao && $processo[ST_TRAMITACAO_SIMPLIFICADA] != SimNaoEnum::SIM) {

            # se tem CO_USU_FUNC_REPRESENTANTE, foi distribuído
            $coUsuFuncRepresentante = $processo[CO_USU_FUNC_REPRESENTANTE];

            # se o processo não foi distribuído e o usuário logado é o Gestor / Responsável OU
            # se já foi distribuído e o usuário logado é o representante da unidade com o processo
            if ((!$coUsuFuncRepresentante && !empty($entGestorResponsavel))
                || ($coUsuFuncRepresentante && $identity->coUsuarioFuncao == $coUsuFuncRepresentante)) {
                $acoes .=
                    "<a href=\"/distribuicao-processo/distribuir/$coProcesso\">"
                    . $this->montaIcone('glyphicon glyphicon-sort', 'Distribuir Processo') .
                    "</a>";

                $acoes .=
                    "<a href=\"/comissao/compor-comissao/$coProcesso\">"
                    . $this->montaIcone('fa fa-users', 'Compor Comissão') .
                    "</a>";

                $acoes .=
                    "<a href=\"/comissao/vincular-processo/$coProcesso\">"
                    . $this->montaIcone('fa fa-link', 'Vincular Processo à Comissão') .
                    "</a>";
            }
        }

        /** @var TbPcbConselho $entRepConselho */
        $entRepConselho = $srvConselho->findOneBy([
            CO_IES => $identity->coEmec,
            TP_PROCEDIMENTO => TipoFuncaoEnum::retornarTpProcedimentoPorTipoSolicitacao(
                $processo[CO_TIPO_SOLICITACAO]
            ),
            ST_CONVITE_REPRESENTANTE => StatusConviteEnum::ACEITO,
            CO_USU_FUNC_REPRESENTANTE => $identity->coUsuarioFuncao
        ]);

        if ($coComissao) {
            /** @var TbPcbIntegranteComissao $entPresidenteComissao */
            $entPresidenteComissao = $srvIntegranteComissao->findOneBy([
                CO_COMISSAO => $coComissao,
                ST_PRESIDENTE => SimNaoEnum::SIM,
                ST_CONVITE => CONVITE_ACEITO,
                CO_USU_FUNC_INTEGRANTE => $identity->coUsuarioFuncao
            ]);

            if ($processo[CO_USU_FUNC_CRIACAO] == $identity->coUsuarioFuncao) {
                $acoes .=
                    "<a href=\"/comissao/editar-comissao/$coComissao\">"
                    . $this->montaIcone('fa fa-pencil-square-o', 'Editar Comissão') .
                    "</a>";
            }
            # se tiver na situação correta e o usuário for o presidente da comissão, pode analisar o processo
            if ($coSituacaoProcesso == SituacaoProcessoEnum::ANALISE_SUBSTANTIVA
                && !empty($entPresidenteComissao)) {
                $acoes .=
                    "<a href=\"/analise-substantiva/analisar/$coProcesso\">"
                    . $this->montaIcone(static::FA_FA_LIST, 'Analisar Processo') .
                    "</a>";
            }
            # se tiver na situação correta e o usuário for o presidente da comissão, pode analisar os dados
            if ($coSituacaoProcesso == SituacaoProcessoEnum::AGUARDANDO_VALIDACAO_INFO_COMPLEMENTARES
                && !empty($entPresidenteComissao)) {
                $acoes .=
                    "<a href=\"/analise-substantiva/analisar-dados-complementares/$coProcesso\">"
                    . $this->montaIcone(static::FA_FA_LIST, 'Analisar Dados Complementares') .
                    "</a>";
            }
            # se tiver na situação correta e o usuário for o presidente da comissão, pode elaborar parecer da comissão
            if ($coSituacaoProcesso == SituacaoProcessoEnum::AGUARDANDO_PARECER_COMISSAO
                && !empty($entPresidenteComissao)) {
                $acoes .=
                    "<a href=\"/parecer-comissao/elaborar-parecer/$coProcesso\">"
                    . $this->montaIcone(static::FA_FA_LIST, 'Elaborar Parecer da Comissão') .
                    "</a>";
            }
            # se tiver na situação de Encaminhado para o Conselho / Câmara
            if ($coSituacaoProcesso == SituacaoProcessoEnum::ENCAMINHADO_CONSELHO_CAMARA) {
                # se (não houver relator ou se o convidado tiver recusado) e o usuário logado for o repres. do conselho,
                # pode selecionar/convidar relator
                if ((empty($processo[CO_RELATOR_PROCESSO])
                        || $processo['stConviteRelatorProcesso'] == StatusConviteEnum::REJEITADO)
                    && !empty($entRepConselho)) {
                    $acoes .=
                        "<a href=\"/relator-processo/selecionar-relator/$coProcesso\">"
                        . $this->montaIcone('fa fa-user', 'Selecionar Relator') .
                        "</a>";
                }
            }
            $arSituacoesParecerRelator = [
                SituacaoProcessoEnum::AGUARDANDO_PARECER_RELATOR,
                SituacaoProcessoEnum::PARECER_RELATOR_INICIADO
            ];
            # se estiver na situação de aguardando parecer do relator e tiver relator com convite aceito
            if (in_array($coSituacaoProcesso, $arSituacoesParecerRelator)
                && !empty($processo[CO_RELATOR_PROCESSO])
                && $processo['stConviteRelatorProcesso'] == StatusConviteEnum::ACEITO) {

                /** @var TbPcbRelatorProcesso $entRelatorProcesso */
                $entRelatorProcesso = $srvRelatorProcesso->find($processo[CO_RELATOR_PROCESSO]);

                # se o integrante relator for o mesmo do usuário logado, pode elaborar o parecer do relator
                if ($entRelatorProcesso->getCoIntegranteConselho()->getCoUsuFuncIntegrante()->getCoUsuarioFuncao()
                    == $identity->coUsuarioFuncao) {
                    $acoes .=
                        "<a href=\"/parecer-relator/elaborar-parecer/$coProcesso\">"
                        . $this->montaIcone(static::FA_FA_LIST, 'Inserir Parecer do Relator') .
                        "</a>";
                }
            }
        }

        $arSituacoesParecerFinal = [
            SituacaoProcessoEnum::AGUARDANDO_DECISAO_FINAL,
            SituacaoProcessoEnum::PARECER_DECISAO_FINAL_INICIADO
        ];
        # se tiver numa das 2 situações e o usuário logado for o repres. do conselho, pode elaborar parecer final
        if (in_array($coSituacaoProcesso, $arSituacoesParecerFinal) && !empty($entRepConselho)) {
            $acoes .=
                "<a href=\"/parecer-final/elaborar-parecer/$coProcesso\">"
                . $this->montaIcone(static::FA_FA_LIST, 'Inserir Decisão Final') .
                "</a>";
        }

        if ($processo[CO_ETAPA] == EtapaEnum::HOMOLOGACAO) {
            if ($coSituacaoProcesso == SituacaoProcessoEnum::AGUARDANDO_HOMOLOGACAO) {
                $acoes .=
                    "<a href=\"/homologacao/verificar-encaminhamento/$coProcesso\">"
                    . $this->montaIcone('fa fa-check-circle-o', 'Verificar Encaminhamento') .
                    "</a>";
            }
            $arSituacoesVerificarAutenticidade = [
                SituacaoProcessoEnum::AGUARDANDO_DOC_ORIGINAL,
                SituacaoProcessoEnum::VALIDACAO_DOCUMENTAL_INICIADA
            ];
            if (in_array($coSituacaoProcesso, $arSituacoesVerificarAutenticidade)) {
                $acoes .=
                    "<a href=\"/homologacao/verificar-autenticidade/$coProcesso\">"
                    . $this->montaIcone('fa fa-check-square-o', 'Verificar Autenticidade') .
                    "</a>";
            }
        }

        if ($processo[CO_RECURSO]) {
            //if ($processo[CO_ETAPA] == EtapaEnum::RECURSO) {
                $arSituacoesAdmissibilidade = [
                    SituacaoProcessoEnum::AGUARDANDO_ANALISE_ADMISSIBILIDADE,
                    SituacaoProcessoEnum::ANALISE_ADMISSIBILIDADE_INICIADA
                ];
                $arSituacoesAnalisar = [
                    SituacaoProcessoEnum::AGUARDANDO_ANALISE_ACADEMICA_RECURSO,
                    SituacaoProcessoEnum::ANALISE_RECURSO_INICIADA
                ];

                # se tiver na situação correta e for o Gestor Responsável
                if (in_array($coSituacaoProcesso, $arSituacoesAdmissibilidade) && $entGestorResponsavel) {
                    $acoes .=
                        "<a href=\"/recurso/analisar-admissibilidade/{$processo[CO_RECURSO]}\">"
                        . $this->montaIcone('fa fa-gavel', 'Analisar Admissibilidade', 'color:red') .
                        "</a>";
                } elseif (in_array($coSituacaoProcesso, $arSituacoesAnalisar) && $entRepConselho) {
                    # se tiver na situação correta e for o Representante do Conselho/Câmara
                    $acoes .=
                        "<a href=\"/recurso/analisar/{$processo[CO_RECURSO]}\">"
                        . $this->montaIcone(static::FA_FA_LIST, 'Analisar Recurso', 'color:red') .
                        "</a>";
                } elseif($coSituacaoProcesso == SituacaoProcessoEnum::AGUARDANDO_HOMOLOGACAO) {
                    $acoes .=
                        "<a href=\"/homologacao/verificar-encaminhamento/$coProcesso\">"
                        . $this->montaIcone('fa fa-check-circle-o', 'Verificar Encaminhamento') .
                        "</a>";
                }
            //}
        }

        return $acoes;
    }

    /**
     * @param array $dados
     * @return string
     */
    public function buscarAcoesRequerenteProcesso($dados)
    {
        $coProcesso = $dados[CO_PROCESSO];
        $coEtapa = $dados[CO_ETAPA];
        $coSituacaoProcesso = $dados[CO_SITUACAO_PROCESSO];
        $coIes = $dados[CO_IES];

        $acoes =
            "<a href=\"/processo/detalharProcesso/$coProcesso\">"
            . $this->montaIcone(static::FA_FA_EYE, static::DETALHAR_PROCESSO) .
            "</a>";

        $acoes .=
            "<a href=\"javascript:void(0)\" onclick=\"visualizarCalendarioIes($coIes); false\">"
            . $this->montaIcone('fa fa-calendar', 'Consultar Recessos') .
            "</a>";

        if ($coEtapa == EtapaEnum::ANALISE_ACADEMICA) {
            if ($coSituacaoProcesso == SituacaoProcessoEnum::AGUARDANDO_ENVIO_INFO_COMPLEMENTARES) {
                $acoes .=
                    "<a href=\"/analise-substantiva/complementar-informacoes/$coProcesso\">"
                    . $this->montaIcone('fa fa-undo', 'Enviar Informações Complementares') .
                    "</a>";
            }

            $arSituacoesSuspenso = [
                SituacaoProcessoEnum::SUSPENSO_90_DIAS,
                SituacaoProcessoEnum::SUSPENSO_INDETERMINADAMENTE
            ];
            if (in_array($coSituacaoProcesso, $arSituacoesSuspenso)) {
                $acoes .=
                    "<a href=\"javascript:void(0)\" onclick=\"reativarProcesso($coProcesso);false\">"
                    . $this->montaIcone('fa fa-play-circle-o', 'Reativar Processo') .
                    "</a>";
            }
        }

        /** @var RecursoService $srvRecurso */
        $srvRecurso = $this->sm->get(RECURSO_SERVICE);
        $qtdeRecursosIndeferidosRequerente = $srvRecurso->contarRecursosIndeferidosRequerente($dados[CO_DIPLOMADO]);

        if (($coEtapa == EtapaEnum::HOMOLOGACAO && $coSituacaoProcesso == SituacaoProcessoEnum::PROCESSO_INDEFERIDO)
            ||
            ($coEtapa == EtapaEnum::RECURSO && $coSituacaoProcesso == SituacaoProcessoEnum::RECURSO_INICIADO)
            && ($qtdeRecursosIndeferidosRequerente < 2)) {
            $acoes .=
                "<a href=\"/recurso/solicitar/$coProcesso\">"
                . $this->montaIcone('fa fa-gavel', 'Solicitar Recurso', 'color:red') .
                "</a>";
        }

        return $acoes;
    }

    /**
     * @param array $dados
     * @return string
     */
    public function buscarAcoesRequerenteSolicitacao($dados)
    {
        $acoes = '';
        $coEtapa = $dados[CO_ETAPA];
        $coSituacaoProcesso = $dados[CO_SITUACAO_PROCESSO];
        $coProcesso = $dados[CO_PROCESSO];

        if ($coEtapa == EtapaEnum::SOLICITACAO && $coSituacaoProcesso == SituacaoProcessoEnum::INICIADA) {
            $acoes .=
                "<a href=\"/processo/identificar-curso/$coProcesso\">"
                . $this->montaIcone('fa fa-pencil', 'Editar Solicitação') .
                "</a>";
        }
        if ($coEtapa == EtapaEnum::PRE_ANALISE && $coSituacaoProcesso == SituacaoProcessoEnum::AGUARDANDO_REENVIO) {
            $acoes .=
                "<a href=\"/reenvio-documentacao/$coProcesso\">"
                . $this->montaIcone('fa fa-undo', 'Reenviar Solicitação') .
                "</a>";
        }
        if ($coEtapa == EtapaEnum::PAGAMENTO
            && $coSituacaoProcesso == SituacaoProcessoEnum::AGUARDANDO_ENVIO_COMPROVANTE_PAGTO) {
            $acoes .=
                "<a href=\"/comprovante-pagamento/$coProcesso\">"
                . $this->montaIcone('fa fa-outdent', 'Anexar Comprovante de Pagamento') .
                "</a>";
        }
        //VERIFICAR QUAL A CONDIÇÃO PARA APRESENTAÇÃO DO ICONE DE CONSULTA IES PARA ALTERAÇÃO OU CADASTRO DE NOVA IES
        if ($dados[ST_VALIDADA] === IesEstrangeiraEnum::REPROVADA) {
            $acoes .=
                "<a href=\"/ies-estrangeira/consultar/$coProcesso\">"
                . $this->montaIcone('fa fa-tasks', 'Alterar Instituição Estrangeira') .
                "</a>";
        }

        $arEtapas = [
            EtapaEnum::SOLICITACAO,
            EtapaEnum::PRE_ANALISE,
            EtapaEnum::PAGAMENTO
        ];

        if ($coSituacaoProcesso == SituacaoProcessoEnum::CANCELADA && in_array($coEtapa, $arEtapas)) {
            $acoes .=
                "<a href=\"javascript:void(0)\" onclick=\"excluirSolicitacao($coProcesso);false\">"
                . $this->montaIcone('glyphicon glyphicon-trash', 'Excluir Solicitação') .
                "</a>";
        }

        $arSituacoesPagamento = [
            SituacaoProcessoEnum::AGUARDANDO_ANEXO_PAGTO,
            SituacaoProcessoEnum::AGUARDANDO_ENVIO_COMPROVANTE_PAGTO
        ];
        $arSituacaoCancelada = [
            SituacaoProcessoEnum::CANCELADA_IES,
            SituacaoProcessoEnum::CANCELADA
        ];
        if (($coEtapa == EtapaEnum::PRE_ANALISE && !in_array($coSituacaoProcesso, $arSituacaoCancelada))
            || ($coEtapa == EtapaEnum::PAGAMENTO && in_array($coSituacaoProcesso, $arSituacoesPagamento))) {
            $acoes .=
                "<a href=\"#\" onclick=\"cancelaSolicitacao($coProcesso);\">"
                . $this->montaIcone('fa fa-minus-circle', 'Cancelar Solicitação') .
                "</a>";
        }
        if (($coEtapa == EtapaEnum::PAGAMENTO && $coSituacaoProcesso != SituacaoProcessoEnum::CANCELADA)
            || ($coEtapa == EtapaEnum::PRE_ANALISE && $coSituacaoProcesso == SituacaoProcessoEnum::CANCELADA_IES)) {
            $acoes .=
                "<a href=\"/processo/detalharProcesso/$coProcesso\">"
                . $this->montaIcone(static::FA_FA_EYE, static::DETALHAR_PROCESSO) .
                "</a>";
        }

        return $acoes;
    }

    /**
     * @param $class
     * @param $title
     * @param null $style
     * @return string
     */
    private function montaIcone($class, $title, $style = null)
    {
        return "<i style=\"$style\" class=\"$class\" data-toggle=\"tooltip\" title=\"$title\" data-original-title=\"$title\"></i>";
    }
}
