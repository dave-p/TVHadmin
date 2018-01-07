<?php
  $page_title = 'Recordings';
  include_once './head.php';
  if (isset($_GET["uuid"])) {
    $uuid = $_GET["uuid"];
    $url = "$urlp/api/dvr/entry/remove?uuid=$uuid";
    file_get_contents($url);
  }
  if (!isset($sort)) $sort = 0;
  if (isset($_POST['last_sort'])) $sort = 1 - $_POST['last_sort'];
  echo "
    <div id='layout'>
      <form name='order' method='POST' action='recordings.php'>
        <table id='heading'>
	  <tr><td class='col_title'><h1>Recordings (Sorted By {$orders[$sort]})</h1></td>
	    <td><input type='submit' name='sort' value='Sort by {$orders[1-$sort]}'>
	     <input type='hidden' name='last_sort' value='$sort'></td>
	  </tr>
	</table>
      </form>
      <table class='list'>
        <tr class='heading'><td class='col_date'><h2>Date</h2></td>
          <td class='col_time'><h2>Time</h2></td>
	  <td class='col_channel'><h2>Channel</h2></td>
          <td class='col_length'><h2>Length</h2></td>
          <td class='col_name'><h2>Name</h2></td>
	  <td class='col_delete'></td>
	  <td class='col_stream'></td>
	</tr>
  ";
        $recordings = get_recordings($sort);
	$i = 0;
	foreach($recordings as $t) {
		$time = strftime("%H:%M", $t["start"]);
		$date = strftime("%a %e/%m/%y", $t["start"]);
		$duration = $t["stop_real"] - $t["start_real"];
		$hh = $duration / 3600;
		$mm = ($duration % 3600) / 60;
		if (strpos($t['status'], 'OK')) {
			if ($i % 2) {
				echo "<tr class='row_odd'>";
			}
			else {
				echo "<tr class='row_even'>";
			}
		}
		else {
			echo "<tr class='row_error'>";
		}
		if ($settings['SUMM'] == 'summary') {
			$summ = 'disp_description';
		}
		else {
			$summ = 'disp_subtitle';
		}
		echo "
	  <td class='col_date'>$date</td>
	  <td class='col_time'>$time</td>
	  <td class='col_channel'>{$t['channelname']}</td>";
		printf("<td class='col_length'>%d:%02d</td>", $hh, $mm);
		echo "
	  <td class='col_name'><div class='epg_title'>{$t['disp_title']}</div><div class='epg_subtitle'>{$t[$summ]}</div></td>
	  <td class='col_delete'><a href='recordings.php?uuid={$t['uuid']}'><img src='images\delete.png' title='Delete Recording'></a></td>
	  <td class='col_stream'><a href='$urlp/play/dvrfile/{$t['uuid']}?title={$t['disp_title']}'><img src='images\play.png' title='Play'></a></td>
	</tr>";
		$i++;
	}
?>
     </table>
    </div>
   </div>
  </body>
</html>
