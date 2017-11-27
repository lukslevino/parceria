<?php
namespace Application\Util;

class MensagemUtil
{
    private static $messages = [
        'MSG001' => 'O %s é inválido.',
        'MSG002' => 'Registro salvo com sucesso.',
        'MSG003' => 'O campo %s é de preenchimento obrigatório.',
        'MSG004' => 'Nenhum registro encontrado.',
        'MSG005' => 'Nenhum campo informado.',
        'MSG006' => 'Pelo menos um campo deverá ser informado.',
        'MSG007' => 'E-mail enviado com sucesso.',
        'MSG008' => 'Informações reenviadas com sucesso.',
        'MSG011' => 'Deseja realmente excluir o documento?',
        'MSG012' => 'A Data de Início da Inscrição não pode ser maior que a Data de Fim da Inscrição.',
        'MSG013' => 'Inclusão realizada com sucesso.',
        'MSG014' => 'Alteração realizada com sucesso.',
        'MSG017' => 'O <b>E-mail</b> deve ser do tipo xxxx@xxxx.xx .',
        'MSG018' => 'Convite(s) enviado(s) com sucesso.',
        'MSG034' => 'Data no formato inválido.',
        'MSG036' => 'Registro excluído com sucesso.',
        'GNR001' => 'Operação realizada com sucesso.',
        'GNR002' => 'Nao foi possivel realizar a operaçao.',
        'MSG039' => 'Campos de E-mail não conferem.',
        'MSG040' => 'Campos de Senha não conferem.',
        'MSG042' => 'E-mail com formato inválido.',
        'MSG076' => 'Sua sessão expirou.',
        'MSG083' => 'Você não possui acesso a esta funcionalidade.',
        'MSG084' => 'A Data Início não pode ser anterior a data atual',
        'MSG085' => 'A Data Fim não pode ser anterior a data atual',
        'MSG086' => 'A Data Fim não pode ser anterior a Data Início',
        'MSG087' => 'Acesso indevido.',
        'MSG088' => 'Inativado com sucesso.',
        'MSG089' =>
            'A <b>Data de Início da Análise</b> não pode ser <b>maior</b> que a <b>Data de Conclusão da Análise</b>.',
        'MSG090'=>'Pelo menos um convite deverá ser adicionado',
    ];

    public static function getMessages()
    {
        return static::$messages;
    }

    public static function getMensagemErroUpload($erros)
    {

        $retornoErros = [];
        $mensagens = [
            'fileExtensionFalse' => MensagemUtil::getMensagem('MSG038'),
            'fileSizeTooBig' => MensagemUtil::getMensagem('MSG037'),
            'fileUploadErrorIniSize' => MensagemUtil::getMensagem('MSG037'),
            'fileSizeTooSmall' => MensagemUtil::getMensagem('MSG038'),
        ];

        if (!empty($erros)) {
            foreach ($erros as $excecao) {
                $retornoErros[key($excecao)] = $mensagens[key($excecao)];
            }
        }

        return $retornoErros;

    }

    public static function getMensagem($codigo, $substituicao = [])
    {
        $msg = static::$messages[$codigo];
        return (empty($substituicao)) ? $msg : vsprintf($msg, $substituicao);
    }
}
