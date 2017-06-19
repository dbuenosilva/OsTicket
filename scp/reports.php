<?php
/*********************************************************************
    reports.php

    System Reports
    Author: Adryano Almeida <adryanoalf@gmail.com>
    Last modification: 30/05/2017
**********************************************************************/
require('admin.inc.php');



$page='report-form.inc.php';
$nav->setTabActive('dashboard');
$ost->addExtraHeader('<meta name="tip-namespace" content="dashboard.system_logs" />',
    "$('#content').data('tipNamespace', 'dashboard.reports');");
require(STAFFINC_DIR.'header.inc.php');
include_once(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
