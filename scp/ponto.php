<?php
/*********************************************************************
 * customizado por Thiago Sales
 * 09/10/2015
 * 
**********************************************************************/
//require('admin.inc.php');
require('staff.inc.php');
require_once(INCLUDE_DIR.'class.ticket.php');
require_once(INCLUDE_DIR.'class.dept.php');
require_once(INCLUDE_DIR.'class.filter.php');
require_once(INCLUDE_DIR.'class.canned.php');
require_once(INCLUDE_DIR.'class.json.php');
require_once(INCLUDE_DIR.'class.dynamic_forms.php');
require_once(INCLUDE_DIR.'class.export.php');      
require_once(STAFFINC_DIR.'header.inc.php'); 


  $sql='SELECT  '
            .' thread.body as menssagem, '
            .' ticket.number as numero,'
            .' thread.title as titulo, '
            .' thread.poster as usuario,'
            .' thread.1entrada as 1entrada,'
            .' thread.1saida as 1saida,'
            .' thread.2entrada as 2entrada,'
            .' thread.2saida as 2saida,'
            .' thread.3entrada as 3entrada,'
            .' thread.3saida as 3saida'
            .' FROM '.TICKET_TABLE.' ticket '
            .' LEFT JOIN '.DEPT_TABLE.' dept ON (ticket.dept_id=dept.dept_id) '
            .' LEFT JOIN '.TICKET_THREAD_TABLE.' thread ON (ticket.ticket_id=thread.ticket_id) and thread.thread_type="R" '
 			.' WHERE thread.1entrada > "0000-00-00 00:00:00"'
            .' GROUP BY thread.poster';
			
		echo '<table width="940" cellpadding="2" cellspacing="0" border="0">';
    	echo '<tr>';
       	echo '<td width="10%" class="has_bottom_border"><strong>Usuario</strong></td>';
		echo '<td width="10%" class="has_bottom_border"><strong>1º Entrada</strong></td>';
		echo '<td width="10%" class="has_bottom_border"><strong>1º Saída</strong></td>';
		echo '<td width="10%" class="has_bottom_border"><strong>2º Entrada</strong></td>';
		echo '<td width="10%" class="has_bottom_border"><strong>2º Saída</strong></td>';
		echo '<td width="10%" class="has_bottom_border"><strong>3º Entrada</strong></td>';
		echo '<td width="10%" class="has_bottom_border"><strong>3º Saída</strong></td>';
		echo '<td width="10%" class="has_bottom_border"><Strong>Chamado</Strong></td></tr>';


		$i = 0;
		
		if(($res=db_query($sql)) && db_num_rows($res)) {
			while($resultado=db_fetch_array($res)) {
        	
				echo '<tr>';
		        echo('<td width="10%" class="has_bottom_border">');
		        echo ($resultado['usuario']);
				echo ('</td><td class="has_bottom_border">');
		        echo($resultado['1entrada']."</td>");
		        echo ('</td><td class="has_bottom_border">');
		        echo($resultado['1saida']."</td>");
				echo ('</td><td class="has_bottom_border">');
				if($resultado['2entrada'] > '000-00-00 00:00:00'){
		        echo($resultado['2entrada']."</td>");
		        }
				else {
				$resultado['2entrada'] ='-';
				}
				echo ('</td><td class="has_bottom_border">');
		        echo($resultado['2saida']."</td>");
				echo ('</td><td class="has_bottom_border">');
		        echo($resultado['3entrada']."</td>");
				echo ('</td><td class="has_bottom_border">');
		        echo($resultado['3saida']."</td>");
		        echo ('</td><td class="has_bottom_border">');
		        echo($resultado['numero']."</td>");
				echo '</tr>';
			}
		$i++;
				
				echo "<tr></tr>";
				echo "</table>";
	}						


?>


<?php
			

require_once(STAFFINC_DIR.'footer.inc.php');


?>
