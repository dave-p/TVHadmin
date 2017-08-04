<?php
  $page_title = 'Timers';
  include_once './head.php';
?>
 <div id="layout">
  <div id="timer_list">
    <table width="100%" border=0 cellspacing=0 cellpadding=0 id="heading">
      <tr><td class="col_title"><h1>Timers</h1></td></tr></table>
    <table width="100%" border=0 cellpadding=0 class="list hilight"><tr class="heading">
      <td class="col_info"></td>
      <td class="col_channel"><h2>Channel</h2></td>
      <td class="col_date selected"><h2>Date</h2></td>
      <td class="col_start"><h2>Start</h2></td>
      <td class="col_stop"><h2>Stop</h2></td> 
      <td class="col_name"><h2>Name</h2></td> 
      <td class="col_channel"><h2>Mode</h2></td>
      <td class=col_delete></td></tr>
<?php
        $timers = get_timers();
	$i = 0;
	$clashes = array();
	foreach($timers as $t) {
	    $start = strftime("%H:%M", $t["start"]);
	    $stop = strftime("%H:%M", $t["stop"]);
	    $date = strftime("%a %e/%m", $t["start"]);
	    if ($i % 2) {
		echo "<tr class='row_odd'>";
	    }
	    else {
		echo "<tr class='row_even'>";
	    }
	    if (!$t["enabled"]) {
		echo "<td class='col_info'><img src='images/tick_yellow.png'></td>";
	    }
	    elseif (check_timer($timers, $t)) {
		echo "<td class='col_info'><img src='images/tick_green.png'></td>";
	    }
	    else {
		echo "<td class='col_info'><img src='images/tick_red.png'></td>";
		$clashes[] = $t;
	    }
	    printf("<td class='col_channel'>%s</td><td class='col_date selected'>%s</td>", $t["channelname"], $date);
            printf("<td class='col_start'>%s</td><td class='col_stop'>%s</td><td class='col_name'>%s</td>", $start, $stop, $t["disp_title"]);
	    if ($t["autorec"] != "") {
		echo "<td class='col_channel'>Series Link</td>";
	    }
	    else {
		echo "<td class='col_channel'></td>";
	    }
            printf ("<td class='col_delete'><a href='delete-timer.php?uuid=%s'><img src='images\delete.png' title='Delete Timer'></a></td></tr>\n", $t["uuid"]);
	    $i++;
	}
	echo "</table></div>\n";
	foreach ($clashes as $c) {
	    $title = $c["disp_title"];
	    if (preg_match("/^(.*)\.\.\./", $title, $t)) {
		$title = $t[1];
	    }
	    if (preg_match("/^New: (.*)/", $title, $t)) {
		$ts = $t[1];
	    }
	    else {
		$ts = $title;
	    }
	    $poss = search_epg($c["channelname"],$ts);
	    foreach ($poss as $p) {
		if ($p["start"] == $c["start"]) {
		    $alt1 = search_epg("",$ts);
		    echo "<p>Alternatives for \"$ts\"</p><ul>";
		    foreach ($alt1 as $a) {
			$sl = '';
			if (isset($a["deafsigned"])) $sl = '[SL]';
			if ($p["episodeUri"] == $a["episodeUri"]) {
			    $when = strftime("%a %e/%m %H:%M", $a["start"]);
			    if (check_event($timers, $a)) {
				printf("<li>%s %s %s %s</li>", $when,$a["channelName"],$a["title"], $sl);
			    }
			    else {
				printf("<li>%s %s %s %s (CLASH)</li>", $when,$a["channelName"],$a["title"], $sl);
			    }
			}
		    }
		    echo "</ul>";
		    break;
		}
	    }
	}

	function check_timer($timers, $t) {
	    if (count($timers) < 2) return true;
	    $tstart = $t["start"];
	    $tstop = $t["stop"];
	    $tuuid = $t["uuid"];
	    foreach ($timers as $m) {
	      if (!$m["enabled"]) continue;
	      if ($m["uuid"] == $tuuid) continue;
	      if(($tstart >= $m["start"] && $tstart < $m["stop"])
	          ||($m["start"] >= $tstart && $m["start"] < $tstop)) {
		return false;
	      }
	    }
	    return true;
	}

        function check_event($timers, $e) {
            $estart = $e["start"];
            $estop = $e["stop"];
            @$euuid = $e["dvrUuid"];
            foreach ($timers as $t) {
              if (!$t["enabled"]) continue;
              if ($t["uuid"] == $euuid) continue;
              if(($estart >= $t["start"] && $estart < $t["stop"])
                  ||($t["start"] >= $estart && $t["start"] < $estop)) {
                return false;
              }
            }
            return true;
        }
?>
    </div>
   </div>
  </body>
</html>
