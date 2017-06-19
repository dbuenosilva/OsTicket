<?php
/*********************************************************************
    gen_report.php

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
$dstart = "2017-".(date('m')-1)."-01";
$dend   = "2017-".(date('m')-1)."-31";

if (isset($_GET['dstart']) && preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", $_GET['dstart']))
    $dstart = $_GET['dstart'];

if (isset($_GET['dend']) && preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/", $_GET['dend']))
    $dend = $_GET['dend'];

if (isset($_GET['organization']) && is_numeric($_GET['organization']))
    $where .= " and ost_organization.id = '" . $_GET['organization'] . "' ";

if (isset($_GET['department']) && is_numeric($_GET['department'])){
    $empresa = $_GET['department'];
    $where .= " and ost_department.dept_id = '" . $_GET['department'] . "' ";
}

if (isset($_GET['name']) && is_numeric($_GET['name']))
    $where .= " and ost_user.id = '" . $_GET['name'] . "' ";

if (isset($_GET['poster']) && is_numeric($_GET['poster']))
    $where .= " and ost_ticket_thread.staff_id = '" . $_GET['poster'] . "' ";

if (isset($_GET['tipo']) && is_numeric($_GET['tipo'])){
    $where .= " and ost_ticket_thread.tipoatende = '";

    if ($_GET['tipo'] == 0)
     $where .= "Presencial' ";
    else if ($_GET['tipo'] == 1)
      $where .= "Remoto' ";
    else
      $where .= "' ";
}

if (isset($_GET['status']) && is_numeric($_GET['status']))
    $where .= " and ost_ticket.status_id = '" . $_GET['status'] . "' ";

    $q = "select
    ost_department.dept_name as Departamento,
    ost_organization.name as Empresa,
    ost_user.name as Solicitante,
    ost_ticket.number as Ticket,
    ost_ticket_thread.chamado as Chamado_Cliente,
    ost_ticket_status.name as Status,
    ost_ticket__cdata.subject as Assunto,
    ost_ticket_thread.poster as Analista,
    ost_ticket_thread.tipoatende as Tipo_Atendimento,
    date(ost_ticket_thread.1entrada) as Data,
    time(ost_ticket_thread.1entrada) as Hora_1entrada,
    time(ost_ticket_thread.1saida) as Hora_1saida,
    time(ost_ticket_thread.2entrada) as Hora_2entrada,
    time(ost_ticket_thread.2saida) as Hora_2saida,
    time(ost_ticket_thread.3entrada) as Hora_3entrada,
    time(ost_ticket_thread.3saida) as Hora_3saida,
    SEC_TO_TIME(
    TIME_TO_SEC(TIMEDIFF(ost_ticket_thread.1saida, ost_ticket_thread.1entrada)) +
    TIME_TO_SEC(TIMEDIFF(ost_ticket_thread.2saida, ost_ticket_thread.2entrada)) +
    TIME_TO_SEC(TIMEDIFF(ost_ticket_thread.3saida, ost_ticket_thread.3entrada))
    ) as Total_Horas_Atendimento,
    ost_ticket_thread.body as Descritivo
    from ost_ticket_thread
    left join ost_ticket on ost_ticket.ticket_id = ost_ticket_thread.ticket_id
    left join ost_ticket__cdata on ost_ticket__cdata.ticket_id = ost_ticket.ticket_id
    left join ost_ticket_status on ost_ticket_status.id = ost_ticket.status_id
    left join ost_user on ost_user.id = ost_ticket.user_id
    left join ost_organization on ost_organization.id = ost_user.org_id
    left join ost_department on ost_department.dept_id = ost_ticket.dept_id
    where
    ost_ticket_thread.1entrada >= '" . $dstart . " 00:00:00' and
    ost_ticket_thread.1entrada <= '" .  $dend  . " 23:59:59' and
    ( ost_ticket_thread.1entrada <> '0000-00-00 00:00:00'
    or ost_ticket_thread.2entrada <> '0000-00-00 00:00:00'
    or ost_ticket_thread.3entrada <> '0000-00-00 00:00:00' ) " . $where . "
    group by
    ost_department.dept_name,
    ost_organization.name,
    ost_user.name,
    ost_ticket.number,
    ost_ticket_thread.chamado,
    ost_ticket_status.name,
    ost_ticket__cdata.subject,
    ost_ticket_thread.poster,
    ost_ticket_thread.tipoatende,
    ost_ticket_thread.1entrada,
    ost_ticket_thread.1saida,
    ost_ticket_thread.2entrada,
    ost_ticket_thread.2saida,
    ost_ticket_thread.3entrada,
    ost_ticket_thread.3saida,
    ost_ticket_thread.body
    order by
    ost_department.dept_name,
    ost_organization.name,
    ost_ticket_thread.1entrada,
    ost_ticket_thread.ticket_id;";

    $res = db_query($q);

    $report = array();
    $count_total = 0;

    if($res && db_num_rows($res))
        while ($row = db_fetch_array($res)) {
            $ele = array();

            $ele[] = $row['Departamento'];
            $ele[] = $row['Empresa'];
            $ele[] = $row['Solicitante'];
            $ele[] = $row['Ticket'];
            $ele[] = $row['Chamado_Cliente'];
            $ele[] = $row['Status'];
            $ele[] = $row['Assunto'];
            //$ele[] = $row['Item_Ticket'];
            $ele[] = $row['Analista'];
            $ele[] = $row['Tipo_Atendimento'];
            $ele[] = $row['Data'];
            $ele[] = $row['Hora_1entrada'];
            $ele[] = $row['Hora_1saida'];
            $ele[] = $row['Hora_2entrada'];
            $ele[] = $row['Hora_2saida'];
            $ele[] = $row['Hora_3entrada'];
            $ele[] = $row['Hora_3saida'];
            $ele[] = $row['Total_Horas_Atendimento'];
            $ele[] = $wizard->toRichTextObject($row['Descritivo']);

            $count_total += strtotime("1970-01-01 " . $row['Total_Horas_Atendimento'] . " UTC");

            if (isset($empresa))
                $empresa_nome = $row['Departamento'];;

            $report[] = $ele;
        }

        $report[] = array('','','','','','','','','','','','','','','','','','');
        $report[] = array('','', '','','','','','','','','','','','','','Total', timeLength($count_total),'');

        $R=new PHPReport();
        $R->load(array(
                    'id'=>'product',
        			'header'=>array(
        					'dept'=>'Departamento','company'=>'Empresa','name'=>'Solicitante','ticket'=>'Ticket', 'chamado'=> 'Chamado_Cliente',
        					'status'=>'Status','subject'=>'Assunto','poster'=>'Analista','tipoatende'=>'Tipo_Atendimento',
        					'date'=>'Data','1entrada'=>'Hora_1Entrada','1saida'=>'Hora_1Saida','2entrada'=>'Hora_2Entrada',
        					'2saida'=>'Hora_2Saida','3entrada'=>'Hora_3Entrada','3saida'=>'Hora_3Saida',
        					'total'=>'Total_Horas_Atendimento','body'=>'Descritivo'
        				),
        			'config'=>array(
        					'header'=>array(
        						'dept'=>array('width'=>160,'align'=>'left'),
            					'company'=>array('width'=>160,'align'=>'left'),
        						'name'=>array('width'=>125,'align'=>'left'),
        						'ticket'=>array('width'=>60,'align'=>'left'),
        						'chamado'=>array('width'=>120,'align'=>'left'),
        						'status'=>array('width'=>60,'align'=>'left'),
        						'subject'=>array('width'=>500,'align'=>'left'),
        						'poster'=>array('width'=>120,'align'=>'left'),
        						'tipoatende'=>array('width'=>70,'align'=>'left'),
        						'date'=>array('width'=>80,'align'=>'left'),
        						'1entrada'=>array('width'=>70,'align'=>'left'),
        						'1saida'=>array('width'=>70,'align'=>'left'),
        						'2entrada'=>array('width'=>70,'align'=>'left'),
        						'2saida'=>array('width'=>70,'align'=>'left'),
        						'3entrada'=>array('width'=>70,'align'=>'left'),
        						'3saida'=>array('width'=>70,'align'=>'left'),
        						'total'=>array('width'=>70,'align'=>'left'),
        						'body'=>array('width'=>600,'align'=>'left')
        					),
        					'data'=>array(
        						'dept'=>array('align'=>'left', 'valign'=>'top'),
            					'company'=>array('align'=>'left', 'valign'=>'top'),
        						'name'=>array('align'=>'left', 'valign'=>'top'),
        						'ticket'=>array('align'=>'left', 'valign'=>'top'),
        						'chamado'=>array('align'=>'left', 'valign'=>'top'),
        						'status'=>array('align'=>'left', 'valign'=>'top'),
        						'subject'=>array('align'=>'left', 'valign'=>'top'),
        						'poster'=>array('align'=>'left', 'valign'=>'top'),
        						'tipoatende'=>array('align'=>'left', 'valign'=>'top'),
        						'date'=>array('align'=>'right', 'valign'=>'top'),
        						'1entrada'=>array('align'=>'right', 'valign'=>'top'),
        						'1saida'=>array('align'=>'right', 'valign'=>'top'),
        						'2entrada'=>array('align'=>'right', 'valign'=>'top'),
        						'2saida'=>array('align'=>'right', 'valign'=>'top'),
        						'3entrada'=>array('align'=>'right', 'valign'=>'top'),
        						'3saida'=>array('align'=>'right', 'valign'=>'top'),
        						'total'=>array('align'=>'right', 'valign'=>'top'),
        						'body'=>array('align'=>'left', 'valign'=>'top')
        					)
        				),
                    'data'=> $report,
    		        'format'=>array(
    				    '1entrada'=>array('datetime'=>'HH [.:] MM [.:] II'),
        				'1saida'=>  array('datetime'=>'HH [.:] MM [.:] II'),
            			'2entrada'=>array('datetime'=>'HH [.:] MM [.:] II'),
                		'2saida'=>  array('datetime'=>'HH [.:] MM [.:] II'),
        				'3entrada'=>array('datetime'=>'HH [.:] MM [.:] II'),
            			'3saida'=>  array('datetime'=>'HH [.:] MM [.:] II'),
        				'total'=>   array('datetime'=>'[HH] [.:] MM [.:] II')
    				   )
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

        $filename = "Relatorio atendimento " . date("Y-m-d");
        if (isset($empresa))
            $filename .= " - " . $empresa_nome;

        echo $R->render($file, $filename);
?>
