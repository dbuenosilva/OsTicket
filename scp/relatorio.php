<?php
/*********************************************************************
relatorio.php

System report for users
Author: Adryano Almeida <adryanoalf@gmail.com>
Last modification: 13/06/2017
**********************************************************************/

require_once('staff.inc.php');

$staff=Staff::lookup($thisstaff->getId());


$inc='profile.inc.php';
$inc='report-form-user.inc.php';
$nav->setTabActive('dashboard');
$ost->addExtraHeader('<meta name="tip-namespace" content="dashboard.relatorio" />',
    "$('#content').data('tipNamespace', 'dashboard.relatorio');");
require_once(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$inc);
require_once(STAFFINC_DIR.'footer.inc.php');
?>
