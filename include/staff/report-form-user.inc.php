<?php
/*********************************************************************
report-form-user.inc.php

Form for System Reports
Author: Adryano Almeida <adryanoalf@gmail.com>
Last modification: 14/06/2017
**********************************************************************/
if(!defined('OSTSTAFFINC') || !$staff || !$thisstaff) die('Access Denied');
if(!$staff) die('Access Denied!');


$analista = (!$thisstaff->isAdmin() && count($staff->getManagedDepartments()) < 1);

if (count($staff->getDepartments()) < 1) die('Access Denied!');

include_once(INCLUDE_DIR.'class.dept.php');
?>
<!-- ### TICKETS ATENDIDOS ### -->
<h2><?php echo ('Gerar relatório de tickets atendidos'); ?></h2>
<form action="gen_report.php" method="get" id="save" enctype="multipart/form-data">
    <table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
        <thead><tr>
            <th colspan="2">
                <h4><?php echo ('Opções de filtro'); ?></h4>
            </th>
        </tr></thead>
        <tbody>






            <tr>
                <td width="220"><?php echo __('Department'); ?>:</td>
                <td>
                    <span>
                        <select name="department">
                            <?php
                            foreach ($staff->getDepartments() as $v) {
                                    $depart = Dept::lookup($v);
                                    echo var_dump($depart);
                                    echo sprintf('<option value="%d">%s</option>',
                                    $v,
                                    $depart->getName());
                                }
                             ?>
                           </select>
                   </span>
               </td>
            </tr>
















            <?php if ($analista) { ?>
                <input type="hidden" name="poster" value="<?=$staff->getId();?>" />
            <?php } ?>






            <tr>
                <td width="220"><?php echo ('Status'); ?>:</td>
                <td>
                    <span>
                        <select name="status">
                            <option value="">Todos</option>
                            <?php

                            $res = db_query("SELECT * FROM `ost_ticket_status` ORDER BY sort");

                            if($res && db_num_rows($res))
                            while ($row = db_fetch_array($res)) {
                                echo sprintf('<option value="%d">%s</option>',
                                $row['id'],
                                $row['name']);
                            } ?>
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td width="220"><?php
                echo ('Tipo de atendimento'); ?>:</td>
                <td>
                    <span>
                        <select name="tipo">
                            <option value="">Todos</option>
                            <option value="0">Presencial</option>
                            <option value="1">Remoto</option>
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td width="120">
                    <label for="data" class="left">Data (Inicio):</label>
                </td>
                <td>
                    <input name="dstart" id="dtart" class="dp" value="" size="12" autocomplete=OFF/>
                    &nbsp;<font class="error">&nbsp;*&nbsp;</font>
                </td>
            </tr>
            <tr>
                <td width="120">
                    <label for="data" class="left">Data (Final):</label>
                </td>
                <td>
                    <input name="dend" id="dend" class="dp" value="" size="12" autocomplete=OFF/>
                    &nbsp;<font class="error">&nbsp;*&nbsp;</font>
                </td>
            </tr>
            <tr>
                <td width="220"><?php
                echo ('Extenção do arquivo'); ?>:</td>
                <td>
                    <span>
                        <select name="file">
                            <option value="0">Pré-visualizar</option>
                            <option value="1">Excel 2003 (.xls)</option>
                            <option value="2">Excel 2007 (.xlsx)</option>
                        </select>
                    </span>
                    <span class="addele"></span>
                    <input type="hidden" class="token" name="TOKEN" value="" />
                </td>
            </tr>
        </tbody>
    </table>

    <p style="padding-left:250px;">
        <input class="button downloadToken" type="submit" name="submit-button" value="<?php
        echo __('Search'); ?>">
    </p>
</form>

<!-- ### CHAMADOS ABERTOS ### -->
<h2><?php echo ('Gerar relatório de chamados abertos'); ?></h2>
<form action="gen_report_chamados.php" method="get" id="save" enctype="multipart/form-data">
    <table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
        <thead><tr>
            <th colspan="2">
                <h4><?php echo ('Opções de filtro'); ?></h4>
            </th>
        </tr></thead>
        <tbody>
            <tr>
                <td width="220"><?php echo __('Department'); ?>:</td>
                <td>
                    <span>
                        <select name="department">
                            <?php
                                foreach ($staff->getDepartments() as $v) {
                                    $depart = Dept::lookup($v);
                                    echo var_dump($depart);
                                    echo sprintf('<option value="%d">%s</option>',
                                    $v,
                                    $depart->getName());
                                }
                             ?>
                           </select>
                   </span>
               </td>
            </tr>
            <tr>
                <td width="220"><?php echo ('Status'); ?>:</td>
                <td>
                    <span>
                        <select name="status">
                            <option value="">Todos</option>
                            <?php

                            $res = db_query("SELECT * FROM `ost_ticket_status` ORDER BY sort");

                            if($res && db_num_rows($res))
                            while ($row = db_fetch_array($res)) {
                                echo sprintf('<option value="%d">%s</option>',
                                $row['id'],
                                $row['name']);
                            } ?>
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td width="120">
                    <label for="data" class="left">Data (Inicio):</label>
                </td>
                <td>
                    <input name="dstart2" id="dstart2" class="dp" value="" size="12" autocomplete=OFF/>
                    &nbsp;<font class="error">&nbsp;*&nbsp;</font>
                </td>
            </tr>
            <tr>
                <td width="120">
                    <label for="data" class="left">Data (Final):</label>
                </td>
                <td>
                    <input name="dend2" id="dend2" class="dp" value="" size="12" autocomplete=OFF/>
                    &nbsp;<font class="error">&nbsp;*&nbsp;</font>
                </td>
            </tr>
            <tr>
                <td width="220"><?php
                echo ('Extenção do arquivo'); ?>:</td>
                <td>
                    <span>
                        <select name="file">
                            <option value="0">Pré-visualizar</option>
                            <option value="1">Excel 2003 (.xls)</option>
                            <option value="2">Excel 2007 (.xlsx)</option>
                        </select>
                    </span>
                    <span class="addele"></span>
                    <input type="hidden" class="token" name="TOKEN" value="" />
                </td>
            </tr>
        </tbody>
    </table>

    <p style="padding-left:250px;">
        <input class="button downloadToken" type="submit" name="submit-button" value="<?php
        echo __('Search'); ?>">
    </p>
</form>


<script type="text/javascript">

function unblockSubmit() {
    window.clearInterval( downloadTimer );
    expireCookie( "downloadToken" );
    $("#loading").css("display", "none");
    $("#overlay").css("display", "none");
    attempts = 15;
}

function getCookie( name ) {
    var parts = document.cookie.split(name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

function expireCookie( cName ) {
    document.cookie =
    encodeURIComponent(cName) + "=deleted; expires=" + new Date( 0 ).toUTCString();
}

function setFormToken() {
    var downloadToken = new Date().getTime();
    return downloadToken;
}

var downloadTimer;
var attempts = 15;

$('form').submit( function() {
    var downloadToken = setFormToken();
    $(".token").val(downloadToken);

    downloadTimer = setInterval( function() {
        attempts--;
        var token = getCookie( "downloadToken" );
        if( (attempts == 0) || (token == downloadToken) ) {
            unblockSubmit();
        }
    }, 1000 );
    return true;
});

</script>
