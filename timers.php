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
	    if ($v['enabled'] === TRUE) $mode = 'false';
	    else $mode = 'true';
	    $data = urlencode("[{\"enabled\": $mode, \"uuid\": \"$uuid\" }]");
	    $url = "$urlp/api/idnode/save?node=$data";
	    file_get_contents($url);
	    $v['enabled'] = !$v['enabled'];
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
	$muxes = array();
	$clashes = array();
	foreach($timers as $t) {
	    $start = date("H:i", $t["start_real"]);
	    $date = date("D d/m", $t["start_real"]);
	    if (strpos($t["uri"], "#")) {
		$s = get_ms_stop($t);
		$stop = date("H:i", $s);
	    }
	    else $stop = date("H:i", $t["stop_real"]);
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
		    break;
		  case 4:
		    echo "<td class='col_info'><img src='images/spacer.gif'></td>";
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
	    if (!isset($c["uri"])) continue;
	    $title = $c["disp_title"];
	    if (preg_match("/^(.*?)\.\.\./", $title, $t)) {
		$title = $t[1];
	    }
	    if (preg_match("/^New: *(.*)/", $title, $t)) {
		$ts = $t[1];
	    }
	    else {
		$ts = $title;
	    }
	    $alt = search_epg("",$ts);
	    if (count($alt) > 1) {
		$s = '';
		foreach ($alt as $a) {
		    if (isset($a["dvrUuid"]) && $a["dvrUuid"] == $c["uuid"]) continue;
		    $sl = '';
		    if (isset($a["deafsigned"])) $sl = '[SL]';
		    if (isset($a["episodeUri"]) && ($c["uri"] === $a["episodeUri"])) {
			$when = date("D d/m H:i", $a["start"]);
			if (!check_event($timers, $a)) {
			    $s .= "<li>$when {$a["channelName"]} {$a["title"]} $sl</li>";
			}
			else {
			    $s .= "<li>$when {$a["channelName"]} {$a["title"]} $sl(CLASH)</li>";
			}
		    }
		}
		if (strlen($s) > 0) {
		    $dt = date("D d/m \a\\t H:i", $c["start"]);
		    echo "<p>Alternatives for \"$ts\" on $dt</p><ul>$s</ul></p>";
		}
	    }
	}

	function check_timer($timers, $t) {
	    global $settings;
	    if (count($timers) < 2) return 0;
	    if (!$t["enabled"]) return 4;
	    $tstart = $t["start_real"];
	    $tstop = $t["stop_real"];
	    $tuuid = $t["uuid"];
	    $tchannel = $t["channel"];
	    $ret = 0;
	    foreach ($timers as $m) {
	      if (!$m["enabled"]) continue;
	      if ($m["channel"] === $tchannel) continue;
	      if ($m["uuid"] === $tuuid) continue;
	      if (($tstart >= $m["start_real"] && $tstart < $m["stop_real"])
	          ||($m["start_real"] >= $tstart && $m["start_real"] < $tstop)) {
		if (!isset($settings['CLASHDET'])
		  || (get_mux_for_timer($m) === get_mux_for_timer($t))) $ret = max($ret,1);
		else $ret = max($ret,2);
	      }
	    }
	    return $ret;
	}

        function check_event($timers, $e) {
            $estart = $e["start"];
            $estop = $e["stop"];
	    $ret = 0;
            foreach ($timers as $t) {
              if (!$t["enabled"]) continue;
              if(($estart >= $t["start_real"] && $estart < $t["stop_real"])
                  ||($t["start_real"] >= $estart && $t["start_real"] < $estop)) {
		if (get_mux_for_event($e) === get_mux_for_timer($t)) $ret = max($ret,1);
                else $ret = max($ret,2);
              }
            }
            return $ret;
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
		global $urlp, $channels, $muxes;
		if (empty($channels)) {
			$channels = get_channels();
			$muxes = get_muxes();
		}
		foreach ($channels as &$c) {
			if ($ch === $c["uuid"]) break;
		}
		if (isset($c["mux"])) return $c["mux"];
		$svc = $c["services"][0];
		$name = $muxes[$svc];
		$mux = substr($name, 0, strrpos($name, '/'));
		$c["mux"] = $mux;
		return $mux;
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

	function get_muxes() {
		global $urlp;
		$url = "$urlp/api/service/list?enum=1";
		$json = file_get_contents($url);
		$j = json_decode($json, true);
		$recs = &$j["entries"];
		$ret = array();
		foreach ($recs as $r) {
			$ret[$r["key"]] = $r["val"];
		}
		return $ret;
	}

	function get_ms_stop($t) {
		$limit = $t["stop"] + 10800;
		$epg = get_epg($t["channelname"], $t["start"], $limit);
		$ret = $t["stop"];
		foreach ($epg as $e) {
			if ($e["episodeUri"] == $t["uri"]) $ret = $e["stop"];
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
