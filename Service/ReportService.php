<?php

namespace SanSIS\Core\ReportBundle\Service;


use SanSIS\Core\BaseBundle\ServiceLayer\ServiceAbstract;
use SanSIS\Sgp\CoreBundle\Entity\Relatorio;

/**
 * Class ReportService
 * @package SanSIS\Core\ReportBundle\Service
 */
class ReportService extends ServiceAbstract
{

    /**
     * @param null $perfil
     * @return array
     */
    public function getReports($perfil)
    {
        $repositoryRelatorio = $this->entityManager->getRepository('SanSIS\Sgp\CoreBundle\Entity\Relatorio');
        $arrayRelatorios = $repositoryRelatorio->getListRelatorioByPerfil($perfil);

        return $arrayRelatorios;

    }


    /**
     * @param $seqReport
     * @return object
     */
    public function getReportBySeq($seqReport)
    {

        $repositoryRelatorio = $this->entityManager->getRepository('SanSIS\Sgp\CoreBundle\Entity\Relatorio');
        $entityRelatorio = $repositoryRelatorio->findOneBy(array('coSgpRelatorio' => $seqReport));

        return $entityRelatorio;

    }

    /**
     * Método para foormatar os parametros Get enviados para o formato Array
     *
     * @param $strGet
     * @return array
     */
    public function formatGetToArray($strGet)
    {

        $result = array();

        $tmpArray = explode("&", $strGet);

        foreach ($tmpArray as $key => $value) {
            $tmpArray2 = explode("=", $value);
            $result[$tmpArray2[0]] = $tmpArray2[1];
        }

        return $result;

    }

    /**
     *  Este método altera o nome das colunas para o array inserido em Label, caso não seja enviado ele permanecera como esta
     *
     * @param $queryResult
     * @param $queryResultFormatado
     */
    public function formatQueryKeys($queryResult){
        if (array_key_exists('label', $queryResult)) {

            $colNames = $queryResult['label'];
            unset($queryResult['label']);

            $colModel = $queryResult['colModel'];
            unset($queryResult['colModel']);

            $formatArray = array();
            foreach ($queryResult as $resultKey => $result) {
                $j = 0;
                foreach ($result as $key => $value) {
                    if (isset($colNames[$j])) {
                        $formatArray[$resultKey][$colNames[$j]] = $value;
                    } else {
                        $formatArray[$resultKey][$key] = $value;
                    }
                    $j++;
                }
            }
            $queryResultFormatado = $formatArray;
            $queryResultFormatado['colModel'] = $colModel;
        } else {
            $queryResultFormatado = $queryResult;
        }

        return $queryResultFormatado;

    }

} 