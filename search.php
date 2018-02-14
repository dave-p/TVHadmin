<?php
  $page_title = 'Search Results';
  include_once './head.php';
?>
	<script type="text/javascript">
	function formSubmit()
	{
	document.forms.telly.submit()
	}
	</script>
  <div id="layout">
 <?php
	if($_GET['find'] != "") {
		$find = $_GET["find"];
		$timers = get_timers();
		$tevents = array();
		foreach ($timers as $t) {
			$tevents[$t["broadcast"]] = 1;
		}
		echo "
      <table id='heading'>
	<tr>
	  <td class='col_title'>
	    <div id='mobmenu'>&#9776;</div> <h1>Matches for: <i>$find</i></h1>
	  </td>
	</tr>
      </table>
      <table class='list'>";
		$i = 0;
		$last_prog_date = " ";
		$results = search_epg("", $find);
		foreach ($results as $r) {
			$d = date('l d/n', $r["start"]);
                        $t = date('H:i', $r["start"]);
			if ($d != $last_prog_date) {
				echo "<tr class='newday'><td colspan='5'><span class='date_long'>$d</span></td></tr>";	
				$last_prog_date = $d;
			}
			if ($i % 2) {
				echo "<tr class='row_odd'>";
			}
			else {
				echo "<tr class='row_even'>";
			}
			echo "
	  <td class='col_duration'>
	    <span class='time_duration'><span class='time_start'>$t</span></span></td>
	  <td class='col_channel'>
	    <div class='channel_name'>{$r['channelName']}</div></td>
	  <td class='col_center'>
	    <div class='epg_title'>{$r['title']}</div><div class='epg_subtitle'>{$r['summary']}</div></td>";
			$evt = $r["eventId"];
			if (!array_key_exists($evt, $tevents)) {
				echo "<td><a href='record.php?eventId=$evt&series=N&from=3&id=$find'><img src='images/rec_button1.png' alt='record' title='record'></a></td>";
				if (isset($r["serieslinkUri"])) {
					echo "<td><a href='record.php?eventId=$evt&series=Y&from=3&id=$find'><img src='images/rec_buttonS.png' alt='record series' title='record series'></a></td>";
				}
				echo "</tr>\n";
			}
			else {
				echo "<td></td></tr>\n";
			}
			$i++;
		}
		echo "</table>";
	}
?>
   </div>
  </div>
  </body>
</html>
