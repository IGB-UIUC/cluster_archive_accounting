<?php
	require_once 'includes/header.inc.php';
	
	if (!$login_user->is_admin()) {
        exit;
	}
	
	if (isset($_GET['year'])) {
		$year = $_GET['year'];
	}
	else { 
		$month = date('m');
		if ($month < 7) {
			$year = date('Y');			
		} else {
			$year = date('Y') + 1;
		}

	}
	
	$previous_year = $year -1;
	$next_year =$year +1;
	$start_date = $year-1 . "0701";
	$end_date = $year . "0630";
	
	$back_url = $_SERVER['PHP_SELF'] . "?year=" . $previous_year;
	$forward_url = $_SERVER['PHP_SELF'] . "?year=" . $next_year;
	
	$graph_type_array[0]['type'] = 'top_usage_users';
	$graph_type_array[0]['title'] = 'Usage';
	$graph_type_array[1]['type'] = 'top_delta_usage_users';
	$graph_type_array[1]['title'] = '&Delta; Usage';
	$graph_type_array[2]['type'] = 'top_cost_users';
	$graph_type_array[2]['title'] = 'Cost';
	$graph_type_array[3]['type'] = 'top_smallfiles_users';
	$graph_type_array[3]['title'] = 'Small Files';

	$graph_type = $graph_type_array[0]['type'];
	if (isset($_POST['graph_type'])) {
	    $graph_type = $_POST['graph_type'];
	
	}
	$get_array  = array('graph_type'=>$graph_type, 'start_date'=>$start_date, 'end_date'=>$end_date);
	
	$graph_form = "<form class='form-inline' name='select_graph' id='select_graph' method='post' action='" . $_SERVER['PHP_SELF']. "?start_date=" . $start_date . "&end_date=" . $end_date . "'><div class='form-group'><select name='graph_type' class='form-control' onChange='document.select_graph.submit();'>";
	
	foreach ($graph_type_array as $graph) {
        $graph_form .= "<option value='" . $graph['type'] . "' ";
        if ($graph_type == $graph['type']) {
            $graph_form .= "selected='selected'";
        }
        $graph_form .= ">" . $graph['title'] . "</option>\n";
	}
	
	$graph_form .= "</div></select></form>";
	
	$stats = new statistics($db);
	?>
	<h3>Fiscal Yearly Stats - <?php echo $year;?></h3>
	<ul class="pager">
		<li class="previous"><a href="<?php echo $back_url;?>">Previous Year</a></li>
		<?php
            $next_year = strtotime('+1 day', strtotime($end_date));
            $today = mktime(0,0,0,date('m'),date('d'),date('y'));
            if ($next_year > $today) {
                echo "<li class='next disabled'><a href='#'>Next Year</a></li>";
            }
            else {
                echo "<li class='next'><a href='" . $forward_url . "'>Next Year</a></li>";
            }
        ?>
	</ul>
	
	<table class="table table-bordered table-condensed table-striped">
		<tr>
			<td>Usage (TB):</td>
			<td><?php echo $stats->get_total_usage($start_date,$end_date,true);?> TB</td>
		</tr>
		<tr>
			<td>&Delta; Usage (TB):</td>
			<td><?php echo $stats->get_total_delta_usage($start_date,$end_date,true);?> TB</td>
		</tr>
		<tr>
			<td>Cost:</td>
			<td>$<?php echo $stats->get_total_cost($start_date,$end_date,true);?></td>
		</tr>
		<tr>
			<td>Small Files:</td>
			<td><?php echo $stats->get_total_smallfiles($start_date,$end_date,true);?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $graph_form;?></td>
		</tr>
	</tbody>
</table>
<script type="text/javascript" src="includes/graph.inc.js"></script>
<script type="text/javascript">
	google.setOnLoadCallback(function(){drawChart("graph.php?<?php echo http_build_query($get_array);?>");});
</script>
<div id="chart_div"></div>
<?php
	require_once 'includes/footer.inc.php';
?>