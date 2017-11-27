<?php

namespace Application\Util;

class StringUtil
{

    public static function removeMascara($campo)
    {
        return str_replace(array(':', '/', '-', '.'), array('', '', '', ''), $campo);
    }

    public static function mask($val, $mask)
    {
        if (!$val) {
            return null;
        }
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#' && isset($val[$k])) {
                $maskared .= $val[$k++];

            } elseif(isset($mask[$i])) {
                $maskared .= $mask[$i];

            }
        }
        return $maskared;
    }

    public static function removeTagsHtml($html)
    {
        return trim(preg_replace('/(&nbsp;)+|\s\K\s+/', '', strip_tags($html)));
    }

    public static function setValorPadrao($valor, $padrao = "")
    {
        return (!empty($valor)) ? $valor : $padrao;
    }

    public static function unicodeString($str, $encoding = null)
    {
        if (is_null($encoding)) {
            $encoding = ini_get('mbstring.internal_encoding');
        }
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', create_function('$match',
            'return mb_convert_encoding(pack("H*", $match[1]), ' . var_export($encoding, true) . ', "UTF-16BE");'),
            $str);
    }

    public static function removeCaracteresEspeciais($palavra, $removerEspaco = true)
    {
        $string = StringUtil::unicodeString($palavra);
        $original = array(
            "á",
            "à",
            "ã",
            "â",
            "é",
            "ê",
            "í",
            "ó",
            "ô",
            "õ",
            "ú",
            "ü",
            "ç",
            "Á",
            "À",
            "Ã",
            "Â",
            "É",
            "Ê",
            "Í",
            "Ó",
            "Ô",
            "Õ",
            "Ú",
            "Ü",
            "Ç",
        );
        $replaced = array(
            "a",
            "a",
            "a",
            "a",
            "e",
            "e",
            "i",
            "o",
            "o",
            "o",
            "u",
            "u",
            "c",
            "A",
            "A",
            "A",
            "A",
            "E",
            "E",
            "I",
            "O",
            "O",
            "O",
            "U",
            "U",
            "C",
        );
        if ($removerEspaco) {
            $original[] = " ";
            $replaced[] = "_";
        }
        return str_replace($original, $replaced, $string);
    }

    public static function mascaraString($mascara, $string)
    {
        for ($i = 0; $i < strlen($string); $i++) {
            $mascara[strpos($mascara, "#")] = $string[$i];
        }
        return $mascara;
    }

    public static function validaCPF($cpf = null) {

        // Elimina possivel mascara
        $cpf = preg_replace('[^0-9]', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        // Verifica se um número foi informado
        // Verifica se o numero de digitos informados é igual a 11
        // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso
        if (empty($cpf) || strlen($cpf) != 11 || in_array($cpf, ['00000000000',
                                '11111111111' ,
                                '22222222222' ,
                                '33333333333' ,
                                '44444444444' ,
                                '55555555555' ,
                                '66666666666' ,
                                '77777777777' ,
                                '88888888888' ,
                                '99999999999'])
        ) {
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        } else {

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }
}
