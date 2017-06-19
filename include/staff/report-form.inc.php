<?php
/*********************************************************************
report-form.inc.php

Form for System Reports
Author: Adryano Almeida <adryanoalf@gmail.com>
Last modification: 13/06/2017
**********************************************************************/
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() ) die('Access Denied');
$pages = Page::getPages();
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
                            <option value="">Todas</option>
                            <?php

                            $res = db_query("SELECT * FROM `ost_department` ORDER BY dept_name, dept_id");

                            if($res && db_num_rows($res))
                            while ($row = db_fetch_array($res)) {
                                echo sprintf('<option value="%d">%s</option>',
                                $row['dept_id'],
                                $row['dept_name']);
                            } ?>
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td width="220"><?php echo ('Empresa'); ?>:</td>
                <td>
                    <span>
                        <select name="organization">
                            <option value="">Todas</option>
                            <?php

                            $res = db_query("SELECT * FROM `ost_organization` ORDER BY name,id");

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
                <td width="220"><?php echo ('Solicitante'); ?>:</td>
                <td>
                    <span>
                        <select name="name">
                            <option value="">Todos</option>
                            <?php


                            $q = "select ost_user.name as Solicitante, ost_user.id as id
                            from ost_ticket_thread
                            left join ost_ticket on ost_ticket.ticket_id = ost_ticket_thread.ticket_id
                            left join ost_ticket__cdata on ost_ticket__cdata.ticket_id = ost_ticket.ticket_id
                            left join ost_ticket_status on ost_ticket_status.id = ost_ticket.status_id
                            left join ost_user on ost_user.id = ost_ticket.user_id
                            where
                            ( ost_ticket_thread.1entrada <> '0000-00-00 00:00:00'
                            or ost_ticket_thread.2entrada <> '0000-00-00 00:00:00'
                            or ost_ticket_thread.3entrada <> '0000-00-00 00:00:00' )
                            group by
                            ost_user.name
                            order by
                            ost_user.name,
                            ost_ticket_thread.ticket_id;";




                            $res = db_query($q);

                            if($res && db_num_rows($res))
                            while ($row = db_fetch_array($res)) {
                                echo sprintf('<option value="%d">%s</option>',
                                $row['id'],
                                $row['Solicitante']);
                            }
                            ?>
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td width="220"><?php echo ('Analista'); ?>:</td>
                <td>
                    <span>
                        <select name="poster">
                            <option value="">Todos</option>
                            <?php

                            $res = db_query("SELECT * FROM `ost_staff` ORDER BY firstname, staff_id");

                            if($res && db_num_rows($res))
                            while ($row = db_fetch_array($res)) {
                                echo sprintf('<option value="%d">%s</option>',
                                $row['staff_id'],
                                $row['firstname'] . ' ' . $row['lastname']);
                            } ?>
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
                            <option value="">Todas</option>
                            <?php

                            $res = db_query("SELECT * FROM `ost_department` ORDER BY dept_name, dept_id");

                            if($res && db_num_rows($res))
                            while ($row = db_fetch_array($res)) {
                                echo sprintf('<option value="%d">%s</option>',
                                $row['dept_id'],
                                $row['dept_name']);
                            } ?>
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td width="220"><?php echo ('Empresa'); ?>:</td>
                <td>
                    <span>
                        <select name="organization">
                            <option value="">Todas</option>
                            <?php

                            $res = db_query("SELECT * FROM `ost_organization` ORDER BY name,id");

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
                <td width="220"><?php echo ('Solicitante'); ?>:</td>
                <td>
                    <span>
                        <select name="name">
                            <option value="">Todos</option>
                            <?php


                            $q = "select ost_user.name as Solicitante, ost_user.id as id
                            from ost_ticket_thread
                            left join ost_ticket on ost_ticket.ticket_id = ost_ticket_thread.ticket_id
                            left join ost_ticket__cdata on ost_ticket__cdata.ticket_id = ost_ticket.ticket_id
                            left join ost_ticket_status on ost_ticket_status.id = ost_ticket.status_id
                            left join ost_user on ost_user.id = ost_ticket.user_id
                            where
                            ( ost_ticket_thread.1entrada <> '0000-00-00 00:00:00'
                            or ost_ticket_thread.2entrada <> '0000-00-00 00:00:00'
                            or ost_ticket_thread.3entrada <> '0000-00-00 00:00:00' )
                            group by
                            ost_user.name
                            order by
                            ost_user.name,
                            ost_ticket_thread.ticket_id;";




                            $res = db_query($q);

                            if($res && db_num_rows($res))
                            while ($row = db_fetch_array($res)) {
                                echo sprintf('<option value="%d">%s</option>',
                                $row['id'],
                                $row['Solicitante']);
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

<!-- ### USUÁRIOS X ORGANIZAÇÃO ### -->
<h2><?php echo ('Gerar relatório de usuários X organização'); ?></h2>
<form action="gen_report_user_org.php" method="get" id="save" enctype="multipart/form-data">
    <table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
        <thead><tr>
            <th colspan="2">
                <h4><?php echo ('Opções de filtro'); ?></h4>
            </th>
        </tr></thead>
        <tbody>
            <tr>
                <td width="220"><?php echo ('Empresa'); ?>:</td>
                <td>
                    <span>
                        <select name="organization">
                            <option value="">Todas</option>
                            <option value="-1">Nenhuma</option>
                            <?php

                            $res = db_query("SELECT * FROM `ost_organization` ORDER BY name,id");

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
                <td width="220"><?php echo __('User'); ?>:</td>
                <td>
                    <span>
                        <select name="name">
                            <option value="">Todos</option>
                            <?php


                            $q = "select ost_user.name as Solicitante, ost_user.id as id
                            from ost_ticket_thread
                            left join ost_ticket on ost_ticket.ticket_id = ost_ticket_thread.ticket_id
                            left join ost_ticket__cdata on ost_ticket__cdata.ticket_id = ost_ticket.ticket_id
                            left join ost_ticket_status on ost_ticket_status.id = ost_ticket.status_id
                            left join ost_user on ost_user.id = ost_ticket.user_id
                            where
                            ( ost_ticket_thread.1entrada <> '0000-00-00 00:00:00'
                            or ost_ticket_thread.2entrada <> '0000-00-00 00:00:00'
                            or ost_ticket_thread.3entrada <> '0000-00-00 00:00:00' )
                            group by
                            ost_user.name
                            order by
                            ost_user.name,
                            ost_ticket_thread.ticket_id;";




                            $res = db_query($q);

                            if($res && db_num_rows($res))
                            while ($row = db_fetch_array($res)) {
                                echo sprintf('<option value="%d">%s</option>',
                                $row['id'],
                                $row['Solicitante']);
                            }
                            ?>
                        </select>
                    </span>
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
