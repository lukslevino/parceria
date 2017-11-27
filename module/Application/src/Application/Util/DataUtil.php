<?php
namespace Application\Util;

class DataUtil
{

    /*Metodo para somar data
     *$dataInicial = a partir de quando a soma
     *$quantidade = quanto tempo numerico
     *$periodo = d=>dias, m=>mes y=>anos, h=>horas
     */
    public static function somaData(\DateTime $dataInicial, $quantidade, $periodo)
    {


        switch ($periodo) {
            case "d":
                $periodo = "days";
                break;
            case "m":
                $periodo = "months";
                break;
            case "y":
                $periodo = "years";
                break;
            case "h":
                $periodo = "hour";
                break;
        }

        $dataFinal = strtotime($dataInicial->format(Y_M_D_H_I_S) . "+" . $quantidade . " " . $periodo);
        $dataFinal = new \DateTime(date(Y_M_D_H_I_S, $dataFinal));
        return $dataFinal;

    }

    public static function getDataHora()
    {
        return date('d/m/Y H:i');
    }

    /**
     *
     * @param date $data
     * @param string $format
     * @return string $retorno
     */
    public static function convertDate($data, $format = 'USA')
    {
        $arrDate = explode("/", $data);
        $newDate = $arrDate[2] . '-' . $arrDate[1] . '-' . $arrDate[0];

        switch ($format) {

            case 'TIMESTAMP':
                $retorno = strtotime($newDate);
                break;

            case 'USA':
                $retorno = $newDate;
                break;

            default:
                $retorno = $data;
        }

        return $retorno;
    }

    public static function getDataExtenso($dia, $mes, $ano)
    {

        if ($mes >= 1 && $mes <= 06) {
            $mes = DataUtil::semestre1($mes);
        } elseif ($mes > 06 && $mes <= 12) {
            $mes = DataUtil::semestre2($mes);
        }

        $dataExtenso = $dia . " de " . $mes . " de " . $ano;
        return $dataExtenso;
    }

    public static function semestre1($mes)
    {
        switch ($mes) {
            case 1 :
                $retorno = 'Janeiro';
                break;
            case 2 :
                $retorno = 'Fevereiro';
                break;
            case 3 :
                $retorno = 'Marco';
                break;
            case 4 :
                $retorno = 'Abril';
                break;
            case 5 :
                $retorno = 'Maio';
                break;
            case 6 :
                $retorno = 'Junho';
                break;
            default;
                $retorno = '';
                break;
        }

        return $retorno;
    }

    public static function semestre2($mes)
    {
        switch ($mes) {
            case 7 :
                $retorno = 'Julho';
                break;
            case 8 :
                $retorno = 'Agosto';
                break;
            case 9 :
                $retorno = 'Setembro';
                break;
            case 10 :
                $retorno = 'Outubro';
                break;
            case 11 :
                $retorno = 'Novembro';
                break;
            case 12 :
                $retorno = 'Dezembro';
                break;
            default;
                $retorno = '';
                break;
        }
        return $retorno;
    }


    public static function dataMaior($dataInicial, $dataFinal)
    {
        $retorno = true;
        if (!empty($dataInicial) && !empty($dataFinal)) {
            $dataInicial = str_replace("-", "", DataUtil::convertDate($dataInicial));
            $dataFinal = str_replace("-", "", DataUtil::convertDate($dataFinal));

            if ($dataFinal >= $dataInicial) {
                $retorno = true;
            }
            $retorno = false;
        }
        return $retorno;
    }

}