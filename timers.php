<?php
  $page_title = 'Timers';
  include_once './head.php';
  $timers = get_timers();
  $now = time();
  if (isset($_GET["uuid"]) && isset($_GET["func"])) {
    $uuid = $_GET["uuid"];
    foreach($timers as $key => &$v) {
      if ($v['uuid'] == $uuid) {
	switch ($_GET["func"]) {
	  case "delete":
	    if ($v["start_real"] < $now) $url = "$urlp/api/dvr/entry/stop?uuid=$uuid";
	    else $url = "$urlp/api/dvr/entry/cancel?uuid=$uuid";
	    file_get_contents($url);
	    unset($timers[$key]);
	    break 2;
	  case "toggle":
	    if ($v['enabled'] == 'true') $mode = 'false';
	    else $mode = 'true';
	    $data = urlencode("[{\"enabled\": $mode, \"uuid\": \"$uuid\" }]");
	    $url = "$urlp/api/idnode/save?node=$data";
	    file_get_contents($url);
	    $v['enabled'] = $mode;
	    break 2;
	}
      }
    }
  }
  echo "
 <script>
   function Toggle(uuid) {
     window.location.href = 'timers.php?uuid='+uuid+'&func=toggle';
   }
 </script>
 <div id='layout'>
   <div id='banner'>
     <table>
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
	 <td class='wideonly col_start'><h2>Start</h2></td>
	 <td class='wideonly col_stop'><h2>Stop</h2></td>
	 <td class='col_name'><h2>Name</h2></td> 
	 <td class='col_channel'><h2>Mode</h2></td>
	 <td class='col_delete'><h2>En</h2></td>
	 <td class='col_delete'></td>
	</tr>
  ";
	$autorecs = get_autorecs();
	$channels = array();
	$clashes = array();
	foreach($timers as $t) {
	    $start = strftime("%H:%M", $t["start_real"]);
	    $stop = strftime("%H:%M", $t["stop_real"]);
	    $date = strftime("%a %e/%m", $t["start_real"]);
	    $subtitle = $t["disp_extratext"];
	    echo "<tr class='row_alt' title=\"$subtitle\">";
	    if ($t["start_real"] < $now) {
		echo "<td class='col_info'><img src='images/rec.png'></td>";
	    }
	    else {
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
	    }
	    echo "
      <td class='col_channel'>{$t['channelname']}</td>
      <td class='col_date'>$date
	<span class='thinonly'><br />$start-$stop</span></td>
      <td class='wideonly col_start'>$start</td>
      <td class='wideonly col_stop'>$stop</td>
      <td class='col_name'>{$t["disp_title"]}</td>";
	    if ($t["autorec"] != "") {
		$type = "Autorec";
		$type2 = "Autorec";
		if ($autorecs[$t["autorec"]] != "") {
		    $type = "Series Link";
		    $type2 = "Series";
		}
	    }
	    else if ($t["timerec"] != "") {
		$type = "Timed Recording";
		$type2 = "Timer";
	    }
	    else {
		$type = "";
		$type2 = "";
	    }
	    if ($t["enabled"] == "true") {
		$en = "checked";
	    }
	    else {
		$en = "";
	    }
            echo "
      <td class='col_channel'>
	<span class='wideonly'>$type</span>
	<span class='thinonly'>$type2</span>
      </td>
      <td class='col_delete'>
	<input type='checkbox' class='smaller' oninput='Toggle(\"{$t['uuid']}\")' $en>
      </td>
      <td class='col_delete'>
	<a href='timers.php?uuid={$t['uuid']}&func=delete'><img src='images/delete.png' title='Delete Timer'></a>
      </td>
    </tr>\n";
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
		if ($p["start_real"] == $c["start_real"]) {
		    $alt1 = search_epg("",$ts);
		    echo "<p>Alternatives for \"$ts\"</p><ul>";
		    foreach ($alt1 as $a) {
			$sl = '';
			if (isset($a["deafsigned"])) $sl = '[SL]';
			if ($p["episodeUri"] === $a["episodeUri"]) {
			    $when = strftime("%a %e/%m %H:%M", $a["start_real"]);
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
	    $tstart = $t["start_real"];
	    $tstop = $t["stop_real"];
	    $tuuid = $t["uuid"];
	    $tchannel = $t["channel"];
	    foreach ($timers as $m) {
	      if (!$m["enabled"]) continue;
	      if ($m["channel"] === $tchannel) continue;
	      if ($m["uuid"] === $tuuid) continue;
	      if (($tstart >= $m["start_real"] && $tstart < $m["stop_real"])
	          ||($m["start_real"] >= $tstart && $m["start_real"] < $tstop)) {
		if (!isset($settings['CLASHDET'])) return 1;
		if (get_mux_for_timer($m) === get_mux_for_timer($t)) return 1;
		return 2;
	      }
	    }
	    return 0;
	}

        function check_event($timers, $e) {
            $estart = $e["start_real"];
            $estop = $e["stop_real"];
            @$euuid = $e["dvrUuid"];
            foreach ($timers as $t) {
              if (!$t["enabled"]) continue;
              if ($t["uuid"] === $euuid) continue;
              if(($estart >= $t["start_real"] && $estart < $t["stop_real"])
                  ||($t["start_real"] >= $estart && $t["start_real"] < $estop)) {
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
		$recs = &$j["entries"];
		$ret = array();
		foreach ($recs as $r) {
			$ret[$r["uuid"]] = $r["serieslink"];
		}
		return $ret;
	}
?>
	</div>
       </div>
     </div>
   </div>
  </body>
</html>
