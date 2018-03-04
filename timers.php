<?php
  $page_title = 'Timers';
  include_once './head.php';
  if (isset($_GET["uuid"])) {
    $uuid = $_GET["uuid"];
    $url = "$urlp/api/dvr/entry/cancel?uuid=$uuid";
    file_get_contents($url);
  }
  echo "
 <div id='layout'>
   <div id='banner'>
     <table id='heading'>
       <tr>
	<td class='col_title'><div id='mobmenu'>&#9776;</div> <h1>Timers</h1></td>
       </tr>
     </table>
   </div>
   <div id='wrapper'>
     <div id='content'>
       <table class='list'>
	<tr class='heading'>
	 <td class='col_info'></td>
	 <td class='col_channel'><h2>Channel</h2></td>
	 <td class='col_date'><h2>Date</h2></td>
	 <td class='col_start'><h2>Start</h2></td>
	 <td class='col_stop'><h2>Stop</h2></td> 
	 <td class='col_name'><h2>Name</h2></td> 
	 <td class='col_channel'><h2>Mode</h2></td>
	 <td class=col_delete></td>
	</tr>
  ";
	$timers = get_timers();
	$autorecs = get_autorecs();
	$i = 0;
	$channels = array();
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
	    switch(check_timer($timers, $t)) {
	      case 0:
		echo "<td class='col_info'><img src='images/tick_green.png'></td>";
	        break;
	      case 1:
		echo "<td class='col_info'><img src='images/tick_yellow.png'></td>";
		break;
	      case 2: 
		echo "<td class='col_info'><img src='images/tick_red.png'></td>";
		$clashes[] = $t;
	    }
	    echo "
      <td class='col_channel'>{$t['channelname']}</td>
      <td class='col_date selected'>$date</td>
      <td class='col_start'>$start</td>
      <td class='col_stop'>$stop</td>
      <td class='col_name'>{$t["disp_title"]}</td>";
	    if ($t["autorec"] != "") {
		$type = "Autorec";
                foreach ($autorecs as $a) {
		    if ($a["uuid"] == $t["autorec"]) {
			if ($a["serieslink"] != "") {
			    $type = "Series Link";
			}
			break;
		    }
		}
	    }
	    else if ($t["timerec"] != "") {
		$type = "Timed Recording";
	    }
	    else {
		$type = "";
	    }
            echo "
      <td class='col_channel'>$type</td>
      <td class='col_delete'>
	<a href='timers.php?uuid={$t['uuid']}'><img src='images\delete.png' title='Delete Timer'></a>
      </td>
    </tr>\n";
	    $i++;
	}
	echo "</table>\n";
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
			if ($p["episodeUri"] === $a["episodeUri"]) {
			    $when = strftime("%a %e/%m %H:%M", $a["start"]);
			    if (!check_event($timers, $a)) {
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
	    if (count($timers) < 2) return 0;
	    $tstart = $t["start"];
	    $tstop = $t["stop"];
	    $tuuid = $t["uuid"];
	    foreach ($timers as $m) {
	      if (!$m["enabled"]) continue;
	      if ($m["uuid"] === $tuuid) continue;
	      if(($tstart >= $m["start"] && $tstart < $m["stop"])
	          ||($m["start"] >= $tstart && $m["start"] < $tstop)) {
		if(get_mux_for_timer($m) === get_mux_for_timer($t)) return 1;
		else return 2;
	      }
	    }
	    return 0;
	}

        function check_event($timers, $e) {
            $estart = $e["start"];
            $estop = $e["stop"];
            @$euuid = $e["dvrUuid"];
            foreach ($timers as $t) {
              if (!$t["enabled"]) continue;
              if ($t["uuid"] === $euuid) continue;
              if(($estart >= $t["start"] && $estart < $t["stop"])
                  ||($t["start"] >= $estart && $t["start"] < $estop)) {
		if (get_mux_for_event($e) === get_mux_for_timer($t)) return 1;
                else return 2;
              }
            }
            return 0;
        }

	function get_mux_for_event($event) {
		$id = $event["channelUuid"];
		$r = get_mux_for_channel($id);
		return $r;
	}

	function get_mux_for_timer($timer) {
		$id = $timer["channel"];
		$r = get_mux_for_channel($id);
		return $r;
	}

	function get_mux_for_channel($ch) {
		global $urlp, $channels;
		if (empty($channels)) {
			$channels = get_channels();
		}
		foreach ($channels as $c) {
			if ($ch === $c["uuid"]) break;
		}
		$svc = $c["services"][0];
		$url = "$urlp/api/service/streams?uuid=$svc";
		$json = file_get_contents($url);
		$j = json_decode($json, true);
		$name = $j["name"];
		return substr($name, 0, strrpos($name, '/'));
	}

	function get_autorecs() {
		global $urlp;
		$url = "$urlp/api/dvr/autorec/grid?limit=99999";
		$json = file_get_contents($url);
		$j = json_decode($json, true);
		$ret = &$j["entries"];
		return $ret;
	}
?>
	</div>
       </div>
     </div>
   </div>
  </body>
</html>
