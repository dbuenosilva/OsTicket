<?php
/*********************************************************************
    gen_report_user_org.php

    System report generator
    Author: Adryano Almeida <adryanoalf@gmail.com>
    Last modification: 13/06/2017
**********************************************************************/
require('staff.inc.php');

require_once INCLUDE_DIR.'class.note.php';

include '../Classes/PHPExcel.php';
include '../Classes/PHPReport.php';

$rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
$rendererLibrary = 'tcpdf';
$rendererLibraryPath = dirname(__FILE__).'/../Classes/'. $rendererLibrary;

PHPExcel_Settings::setPdfRenderer($rendererName,$rendererLibraryPath);
ini_set('default_charset', 'UTF-8');

$wizard = new PHPExcel_Helper_HTML;

function timeLength($sec)
{
    $s=$sec % 60;
    $m=(($sec-$s) / 60) % 60;
    $h=floor($sec / 3600);
    return $h.":".substr("0".$m,-2).":".substr("0".$s,-2);
}

$where = " ";

if (isset($_GET['organization']) && is_numeric($_GET['organization'])) {
    if ($_GET['organization'] == -1){
        $where .= " and ost_organization.id IS NULL ";
    } else {
        $empresa = $_GET['organization'];
        $where .= " and ost_organization.id = '" . $_GET['organization'] . "' ";
    }
}

if (isset($_GET['name']) && is_numeric($_GET['name']))
    $where .= " and ost_user.id = '" . $_GET['name'] . "' ";


if (isset($_GET['status']) && is_numeric($_GET['status']))
    $where .= " and ost_ticket.status_id = '" . $_GET['status'] . "' ";

    $q = "select
    ost_user.id as ID,
    ost_user.name as Nome,
    ost_user_email.address as Email,
    ost_organization.name as Empresa

    from ost_user
    left join ost_user_email on ost_user_email.user_id = ost_user.id
    left join ost_organization on ost_organization.id = ost_user.org_id
    where 1=1
    " . $where . "
    order by ost_organization.name, ost_user.name;";

    $res = db_query($q);

    $report = array();
    $count_total = 0;

    if($res && db_num_rows($res))
        while ($row = db_fetch_array($res)) {
            $ele = array();

            $ele[] = $row['ID'];
            $ele[] = $row['Nome'];
            $ele[] = $row['Email'];
            $ele[] = $row['Empresa'];

            $empresa_nome = $row['Empresa'];

            $report[] = $ele;
        }

        $R=new PHPReport();
        $R->load(array(
                    'id'=>'product',
        			'header'=>array(
        					'id'=>'ID','name'=>'Nome','email'=>'Email','company'=>'Empresa'
        				),
        			'config'=>array(
        					'header'=>array(
                                'id'=>array('width'=>125,'align'=>'left'),
                                'name'=>array('width'=>125,'align'=>'left'),
                                'email'=>array('width'=>60,'align'=>'left'),
        						'company'=>array('width'=>160,'align'=>'left')
        					),
        					'data'=>array(
                                'id'=>array('align'=>'left', 'valign'=>'top'),
                                'name'=>array('align'=>'left', 'valign'=>'top'),
                                'email'=>array('align'=>'left', 'valign'=>'top'),
        						'company'=>array('align'=>'left', 'valign'=>'top')
        					)
        				),
                    'data'=> $report
                    )
                );

        $file = "html";

        if (isset($_GET['file']) && !empty($_GET['file']) && is_numeric($_GET['file']))
        {
            if ($_GET['file'] == 1)
            $file = "excel2003";
            else if ($_GET['file'] == 2)
            $file = "excel";
        }

        if (isset($_GET['TOKEN'])) {
            $TOKEN = "downloadToken";
            setcookie($TOKEN, $_GET['TOKEN'], time() + (60 * 30), "/");
        }

        $filename = "Relatorio usuários " . date("Y-m-d");
        if (isset($empresa))
            $filename .= " - " . $empresa_nome;

        echo $R->render($file, $filename);
?>
