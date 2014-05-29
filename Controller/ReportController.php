<?php

namespace Ibram\Core\ReportBundle\Controller;

use Ibram\Core\BaseBundle\Controller\ControllerCrudAbstract;
use Ibram\Core\ReportBundle\Service\ReportService;
use Ibram\Sgp\CoreBundle\Entity\Relatorio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReportController
 * @package Ibram\Core\ReportBundle\Controller
 */
class ReportController extends ControllerCrudAbstract
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reportAction()
    {
        $this->checkAccess();

        /** @var ReportService $servReport */
        $servReport = $this->get('Ibram_report.reportService');
        $params['report'] = $servReport->getReports($_SESSION["CO_PERFIL"]);

        array_unshift($params['report'], array(
            'CO_SGP_RELATORIO' => '',
            'DS_RELATORIO' => ' -- Selecione -- '
//            'dsArquivoFormulario' => '',
//            'dsArquivoRelatorio' => '',
//            'dsClasseFormulario' => '',
//            'dsClasseRelatorio' => '',
//            'dsMetodoFormulario' => '',
//            'dsMetodoRelatorio' => ''
        ));

        return $this->render('IbramCoreReportBundle::report.html.twig', $params);
    }

    /**
     * @param $seqReport
     * @return Response
     */
    public function getOutputTemplateAction($seqReport)
    {
        /** @var ReportService $servReport */
        $servReport = $this->get('Ibram_report.reportService');
        $entityReport = $servReport->getReportBySeq($seqReport);

        $serv = $this->get($entityReport->getDsClasseFormulario());
        $metodo = $entityReport->getDsMetodoFormulario();

        return $this->render($entityReport->getDsArquivoFormulario(), $serv->$metodo());
    }

    /**
     * Exibe dados na tela em formato de grid
     *
     * @param $seqReport
     * @return JsonResponse
     */
    public function reportGridAction($seqReport)
    {
        $request = $this->getRequest();

        $page = $request->query->get('page', 1);
        $rows = $request->query->get('rows', 100);

        /** @var ReportService $servReport */
        $servReport = $this->get('Ibram_report.reportService');
        $params = $servReport->formatGetToArray($request->query->get('dataForm'));

        /** @var Relatorio $entityReport */
        $entityReport = $servReport->getReportBySeq($seqReport);

        $serv = $this->get($entityReport->getDsClasseRelatorio());
        $metodo = $entityReport->getDsMetodoRelatorio();
        $query = $serv->$metodo($params);

        $query = $servReport->formatQueryKeys($query);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $page, $rows);

        $response = new \StdClass();
        $response->page = $page;
        $response->total = ceil($pagination->getTotalItemCount());
        $response->records = $pagination->getTotalItemCount();
        $response->rows = $pagination->getItems();

        if (isset($response->rows[0])) {
            $response->colNames = array_keys($response->rows[0]);

            foreach ($response->rows as $k => $row) {
                if (isset($row['Ação']))
                    $response->rows[$k]['Ação'] = $this->getViewGridAction($row['Ação'], $serv->getReportViewRoute());
            }

            if ($query['colModel']) {
                $response->colModel = $query['colModel'];
            } else {
                foreach ($response->colNames as $row) {
                    $response->colModel[]['name'] = $row;
                }
            }
        }

        return new JsonResponse($response);
    }

    /**
     * Gera dados em formato PDF
     *
     * @param $seqReport
     * @return Response
     */
    public function getReportPdfAction($seqReport)
    {
        $return = $this->getResultDinamic($seqReport);
        $return = $this->formatResult($return);

        $facade = $this->get('ps_pdf.facade');
        $response = new Response();

        $this->render($return['arquivoRelatorio'], $return, $response);

        $xml = '<pdf><dynamic-page>';
        $xml .= $response->getContent();
        $xml .= '</dynamic-page></pdf>';
        $content = $facade->render($xml);

        return new Response($content, 200, array('content-type' => 'application/pdf'));

    }

    /**
     * Gera dados em formato HTML
     *
     * @param $seqReport
     * @return Response
     */
    public function getReportHtmlAction($seqReport)
    {
        $result = $this->getResultDinamic($seqReport);
        $result = $this->formatResult($result);

        return $this->render($result['arquivoRelatorio'], $result);

    }

    /**
     * Gera dados em formato do Excel
     *
     * @param $seqReport
     * @return Response
     */
    public function getReportXlsAction($seqReport)
    {
        $result = $this->getResultDinamic($seqReport);
        $result = $this->formatResult($result);

        $response = $this->render($result['arquivoRelatorio'], $result);
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . basename('relatorio-' . date('d-m-Y H:i') . '.xls'));
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        return $response;
    }

    /**
     * Método responsavel por trazer consultar e retornas os dados de uma service dinamicamente
     *
     * @param $seqReport
     * @return mixed
     */
    private function getResultDinamic($seqReport)
    {
        /** @var ReportService $servReport */
        $servReport = $this->get('Ibram_report.reportService');
        /** @var Relatorio $entityReport */
        $entityReport = $servReport->getReportBySeq($seqReport);

        $servOutput = $this->get($entityReport->getDsClasseRelatorio());
        $metodo = $entityReport->getDsMetodoRelatorio();

        $params = $servReport->formatGetToArray($this->getRequest()->query->get('dataForm'));
        $query = $servOutput->$metodo($params);
        $query = $servReport->formatQueryKeys($query);

        if (isset($query[0])) {
            $result['colNames'] = array_keys($query[0]);
        }
        $result['result'] = $query;
        $result['arquivoRelatorio'] = $entityReport->getDsArquivoRelatorio();

        return $result;
    }

    /**
     * Os resultados retornados precisam ser formatados para as exibições que não sejam a Grid
     *
     * @param $return
     * @return mixed
     */
    public function formatResult($return)
    {
        unset($return['colModel']);
        if (count($return['result']) == 1) {
            $return['result'] = null;
        } else {
            /** remove a coluna ação dos resultados, necessario para exibição pdf e excel */
            foreach ($return['result'] as $key => $itemValues) {
                unset($return['result'][$key]['Ação']);
            }

            /** remove as propriedades das colunas do resultado */
            if ($return['result']['colModel']) {
                $return['colModel'] = $return['result']['colModel'];
                unset($return['result']['colModel']);
            }

            /** remove a coluna de Ação da listagem de colunas */
            if ($return['colNames']) {
                $key = array_search('Ação', $return['colNames']);
                if ($key) {
                    unset($return['colNames'][$key]);
                }
            }
        }

        return $return;
    }

    public function checkAccess()
    {
        if(!isset($_SESSION['CO_PERFIL']))
        {
            header('Location: /login.php');
            exit;
        }
    }

} 