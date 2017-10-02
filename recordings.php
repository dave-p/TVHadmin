<?php
  $page_title = 'Recordings';
  include_once './head.php';
  if (!isset($sort)) $sort = 0;
  if (isset($_POST['last_sort'])) $sort = 1 - $_POST['last_sort'];
  echo "
  <div id='rec_list'>
    <div id='topmenu'>
      <form name='order' method='POST' action='recordings.php'>
        <table width='100%' border='0' cellspacing='0' cellpadding='0' id='heading'>
	  <tr><td class='col_title'><h1>Recordings</h1></td>
	    <td><input type='submit' name='sort' value='Sort by {$orders[1-$sort]}'>
	     <input type='hidden' name='last_sort' value='$sort'></td>
	  </tr>
	</table>
      </form>
    </div>
    <div id='layout'>
      <table width='100%' border=0 cellpadding=0 class='list hilight'>
        <tr class='heading'><td class='col_date'><h2>Date</h2></td>
          <td class='col_time'><h2>Time</h2></td>
	  <td class='col_channel'><h2>Channel</h2></td>
          <td class='col_length'><h2>Length</h2></td>
          <td class='col_name'><h2>Name</h2></td>
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
		if ($i % 2) {
			echo "<tr class='row_odd'>";
		}
		else {
			echo "<tr class='row_even'>";
		}
		echo "<td class='col_date'>$date</td>";
		printf("<td class='col_time'>%s</td><td class='col_channel'>%s<td class='col_length'>%d:%02d</td><td class='col_name'><div class='epg_title'>%s</div><div class='epg_subtitle'>%s</div></td>", $time, $t["channelname"], $hh, $mm, $t["disp_title"], $t["disp_description"]);
                echo "</tr>\n";
		$i++;
	}
?>
      </table>
     </div>
    </div>
   </div>
  </body>
</html>
