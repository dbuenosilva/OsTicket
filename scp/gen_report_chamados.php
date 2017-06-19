<?php
/*********************************************************************
    gen_report_chamados.php

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

if (isset($_GET['dstart2']) && preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", $_GET['dstart2']))
    $where .= " and ost_ticket.created >= '" . $_GET['dstart2'] . " 00:00:00' ";

if (isset($_GET['dend2']) && preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", $_GET['dend2']))
    $where .= " and ost_ticket.created <= '" . $_GET['dend2'] . " 23:59:59' ";

if (isset($_GET['department']) && is_numeric($_GET['department'])){
    $empresa = $_GET['department'];
    $where .= " and ost_department.dept_id = '" . $_GET['department'] . "' ";
}

if (isset($_GET['organization']) && is_numeric($_GET['organization']))
    $where .= " and ost_organization.id = '" . $_GET['organization'] . "' ";

if (isset($_GET['name']) && is_numeric($_GET['name']))
    $where .= " and ost_user.id = '" . $_GET['name'] . "' ";

if (isset($_GET['status']) && is_numeric($_GET['status']))
    $where .= " and ost_ticket.status_id = '" . $_GET['status'] . "' ";



    $q = "select
    ost_help_topic.topic as Topico,
    ost_organization.name as Empresa,
    ost_department.dept_name as Departamento,
    ost_user.name as Solicitante,
    ost_ticket.ticket_id,
    ost_ticket.number as Ticket,
    ost_ticket.created,
    ost_ticket_status.name as Status,
    ost_ticket__cdata.subject as Assunto
    from ost_ticket
    left join ost_ticket__cdata on ost_ticket__cdata.ticket_id = ost_ticket.ticket_id
    left join ost_ticket_status on ost_ticket_status.id = ost_ticket.status_id
    left join ost_user on ost_user.id = ost_ticket.user_id
    left join ost_organization on ost_organization.id = ost_user.org_id
    left join ost_department on ost_department.dept_id = ost_ticket.dept_id
    left join ost_help_topic on ost_help_topic.topic_id = ost_ticket.topic_id
    where ost_ticket_status.name <> 'Fechado' and ost_ticket_status.name <> 'Resolvido'
     " . $where . "
    group by
    ost_department.dept_name,
    ost_organization.name,
    ost_user.name,
    ost_ticket.number,
    ost_ticket_status.name,
    ost_ticket__cdata.subject
    order by
    ost_department.dept_name,
    ost_organization.name,
    ost_ticket.created;";

    $res = db_query($q);

    $report = array();

    if($res && db_num_rows($res))
        while ($row = db_fetch_array($res)) {
            $ele = array();

            $ele[] = $row['Topico'];
            $ele[] = $row['Departamento'];
            $ele[] = $row['Empresa'];
            $ele[] = $row['Solicitante'];
            $ele[] = $row['Ticket'];
            $ele[] = $row['created'];
            $ele[] = $row['Status'];
            $ele[] = $row['Assunto'];
            //$ele[] = $row['acao'];


            if (isset($empresa))
                $empresa_nome = $row['Departamento'];;

            $report[] = $ele;
        }

        $R=new PHPReport();
        $R->load(array(
                    'id'=>'product',
        			'header'=>array(
        					'empty'=>'','dept'=>'Departamento','company'=>'Empresa','name'=>'Solicitante','ticket'=>'Ticket', 'created'=> 'Data',
        					'status'=>'Status','subject'=>'Descrição','action'=>'Ação'
        				),
        			'config'=>array(
        					'header'=>array(
                                'empty'=>array('width'=>125,'align'=>'left'),
        						'dept'=>array('width'=>160,'align'=>'left'),
        						'company'=>array('width'=>160,'align'=>'left'),
        						'name'=>array('width'=>125,'align'=>'left'),
        						'ticket'=>array('width'=>60,'align'=>'left'),
        						'created'=>array('width'=>180,'align'=>'left'),
        						'status'=>array('width'=>100,'align'=>'left'),
        						'subject'=>array('width'=>400,'align'=>'left'),
        						'action'=>array('width'=>500,'align'=>'left')
        					),
        					'data'=>array(
                                'empty'=>array('align'=>'left', 'valign'=>'top'),
        						'dept'=>array('align'=>'left', 'valign'=>'top'),
        						'company'=>array('align'=>'left', 'valign'=>'top'),
        						'name'=>array('align'=>'left', 'valign'=>'top'),
        						'ticket'=>array('align'=>'left', 'valign'=>'top'),
        						'created'=>array('align'=>'left', 'valign'=>'top'),
        						'status'=>array('align'=>'left', 'valign'=>'top'),
        						'subject'=>array('align'=>'left', 'valign'=>'top'),
        						'action'=>array('align'=>'left', 'valign'=>'top')
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

        $filename = "Relatorio chamados " . date("Y-m-d");
        if (isset($empresa))
            $filename .= " - " . $empresa_nome;

        echo $R->render($file, $filename);
?>
