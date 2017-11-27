<?php

namespace Application\Util;

class UploadUtil
{
    /**
     * Função para criar um nome único
     *
     * @access public
     * @param String $name
     * @return String
     **/
    public static function rename($name)
    {
        $arquivo = pathinfo($name);
        $extensao = '.' . $arquivo['extension'];
        return $arquivo['filename'] . '_' . md5(date('d-m-Y H:i:s')) . $extensao;
    }

    public static function getNomeOriginalArquivo($string)
    {

        $pathInfo = pathinfo($string);
        $extensao = $pathInfo['extension'];

        if (strripos($string, '_')) {
            $arrString = explode('_', $pathInfo['filename']);
            unset($arrString[(count($arrString) - 1)]);
            return implode('_', $arrString) . '.' . $extensao;
        }

        return $string;
    }
}
