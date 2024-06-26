<?php
	$page_title = "Timeline";
	include_once './head.php';
	if (isset($_GET["eventId"])) {
	  $evt = $_GET["eventId"];
	  if (isset($_GET["series"])) {
	    $url = "$urlp/api/dvr/autorec/create_by_series?event_id=$evt&config_uuid=$config_uuid";
	  }
	  else {
	    $url = "$urlp/api/dvr/entry/create_by_event?event_id=$evt&config_uuid=$config_uuid";
	  }
	  file_get_contents($url);
	}
	$now = time();
	$chans = get_channels();
	$tags = get_channeltags();
	if (array_key_exists('NOANON', $settings)) $view_url = "http://$user:$pass@$ip";
	else $view_url = $urlp;
	if(isset($settings['TIMESPAN'])) {
		$textent = $settings['TIMESPAN'] * 3600;
	}
	else $textent = 14400;
	if(isset($_GET['start'])) {
		$utime = max($_GET['start'], $now);
		if(isset($_GET['right_x'])) $utime += $textent/2;
		else if(isset($_GET['right2_x'])) $utime += 86400;
		else if(isset($_GET['left_x'])) $utime = max($utime-$textent/2, $now);
		else if(isset($_GET['left2_x'])) $utime -= 86400;
	}
	else $utime = $now;
	$toffset = $utime % 1800;	//secs from start of chart to now
	$tstart = $utime - $toffset;
	$tend = $tstart + $textent;
	$tnext = $tend;
	$wday = date('D j M', $utime);
	$media = array();
	if (isset($settings["CSORT"]) && ($settings["CSORT"] == 1)) {
		$lcn = 1;
		$ch_width = 145;
	}
	else {
		$lcn = 0;
		$ch_width = 120;
	}
	if (isset($settings['REFR'])) $refresh = 1;
	else $refresh = 0;
	echo "
 <div id='layout'>
  <div id='banner'>
   <form name='media' method='GET' action='timeline.php'>
    <input type='hidden' name='update' value='1'>
    <input type='hidden' name='start' value='$tstart'>
    <table>
     <tr>
      <td class='col_title'><div id='mobmenu'>&#9776;</div> <h1>Timeline</h1></td>
      <td>";
	foreach ($tags as $t=>$v) {
	  $tt = urlencode($t);
	  if (isset($settings["Tag_$tt"])) {
		echo "
	<div class='media'>
	  <label for='$tt'>$t:</label>
	  <input type='checkbox' name='$tt' id='$tt' onchange='formSubmit()'";
		if (isset($_GET['update'])) {
			if (isset($_GET[$tt])) {
				$media[$t] = $v;
				echo " checked";
			}
		}
		else {
			$g = "Time_" . $tt;
 			if (isset($settings[$g])) {
				$media[$t] = $v;
				echo " checked";
			}
		}
		echo ">
	</div>";
	  }
	}
	echo "
      </td>
      <td>";
	if (($tstart - ($now - $now % 1800)) >= 86400) {
	  echo "<input type='image' alt='Prev Day' title='Prev Day' name='left2' src='images/left2.png'>";
	  echo "<input type='image' name='left2s' src='images/spacer.gif' style='width:10px;height:32px;' disabled>";
	}
	else echo "<input type='image' name='left2' src='images/spacer.gif' style='width:37px;height:32px;' disabled>";
	if ($now < $tstart) {
	  echo "<input type='image' alt='Earlier' title='Earlier' name='left' src='images/left.png'>";
	  echo "<input type='image' name='lefts' src='images/spacer.gif' style='width:10px;height:32px;' disabled>";
	}
	else echo "<input type='image' name='left' src='images/spacer.gif' style='width:30px;height:32px;' disabled>";
	echo "
       <input type='image' alt='Later' title='Later' name='right' src='images/right.png'>
       <input type='image' name='rights' src='images/spacer.gif' style='width:5px;height:32px;' disabled>
       <input type='image' alt='Next Day' title='Next Day' name='right2' src='images/right2.png'>
      </td>
     </tr>
    </table>
   </form>
  </div>
  <div id='wrapper'>
   <div id='timeline'>
    <table class='list' style='table-layout: fixed;'>
     <colgroup>
      <col style='width:{$ch_width}px'>
      <col id='schedules'>
     </colgroup>
     <thead>
      <tr class='newday'>
       <th>$wday</th>
       <th>";
	$t = $tstart;
	for($i=0; $i<4; $i++){
	    $time = date('H:i', $t);
	    echo "
	<div class='row_alt' style='float: left; width: 24.5%;'>$time</div>";
	    $t += $textent / 4;
	}
	echo "
       </th>
      </tr>
     </thead>";
	if (isset($settings["CSORT"]) && ($settings["CSORT"] == 1)) $lcn = 1;
	else $lcn = 0;
	$i = 0;
	foreach($chans as $c) {
	    if (!isset($media["All"])) {
			if (count(array_intersect($c["tags"], $media)) == 0) continue;
	    }
	    $e = get_epg($c["name"], $tstart, $tend);
	    $wd = 98 - count($e)/8;
	    echo "
     <tr>
      <td class='col_channel row_$i'>
       <div class='channel_name'>";
	    if ($lcn) print "{$c['number']} {$c['name']}";
	    else print "{$c['name']}";
	    echo "
       </div>
      </td>
      <td class='col_schedule'>";
	    if (count($e) > 0) {
	      $init = 1;
	      foreach ($e as $p) {
		if ($init) {
		    if ($p['start'] > $tstart) {	#Need a spacer
			$spc = (($p['start'] - $tstart) * $wd) / $textent;
			echo "
	 <div class='spacer' style='width: $spc%;'>
	  <img src='images/spacer.gif' width=1 height=1 alt=''>
	 </div>";
		    }
		    if ($p['start'] <= $now && $p['stop'] > $now) {
		      $colour = 'onNow';
		      $tnext = min($tnext, $p['stop']);
		      $p['onNow'] = 1;
		    }
		    $init = 0;
		}
		else $colour = 'onSoon';
		if (isset($p['dvrState']) && ($p['dvrState'] == 'scheduled' || $p['dvrState'] == 'recording')) {
		    $colour = 'record';
		}
		$duration = min($tend, $p['stop']) - max($tstart, $p['start']);
		if ($duration == 0) continue;
		$pc = ($wd * $duration) / $textent;
		if (isset($p['summary'])) $desc = $p['summary'];
		else $desc = $p['description'];
		@$subtitle = htmlspecialchars($desc, ENT_QUOTES|ENT_HTML5);
		if (isset($p['onNow'])) {
		    if (isset($config_suuid)) $s = "&profile={$config_suuid}";
		    else $s = "";
		    echo "<a href='$view_url/play/ticket/stream/channel/{$c['uuid']}?title={$c['name']}$s' download='{$c['uuid']}.m3u'><div class='item $colour' style='width: $pc%;' title='$subtitle'>{$p['title']}</div></a>";
		}
		else if (isset($p['dvrState'])) echo "<div class='item $colour' style='width: $pc%;' title='$subtitle'>{$p['title']}</div>";
		else {
		    $esctitle = htmlspecialchars($p['title'], ENT_QUOTES|ENT_HTML5);
		    $is_series = isset($p['serieslinkUri']) ? 1 : 0;
		    echo "<div class='item $colour' style='width: $pc%; cursor: pointer;' title='$subtitle' onclick='make_timer(\"{$p['eventId']}\",\"$esctitle\", $is_series)'>{$p['title']}</div>";
		}
	      }
	    }
	    else {
	      if (isset($config_suuid)) $s = "&profile={$config_suuid}";
	      else $s = "";
	      echo "<a href='$view_url/play/ticket/stream/channel/{$c['uuid']}?title={$c['name']}$s' download='{$c['uuid']}.m3u'><div class='item onNow' style='width: 98%;'>No EPG Available</div></a>";
	    }
	    echo "
      </td>
     </tr>";
	    $i = ($i+1)%2;
	}
	echo "
    </table>";
	if(($now >= $tstart) && ($now < $tend)) echo "
    <span id='timenow' style='visibility: hidden'>
     <img src='images/spacer.gif' width='1' height='1' alt=''>
    </span>";
	echo "
    <dialog id='recDialog'>
     <p id='recText'></p>
     <div style='display: flex; justify-content: center;'>
      <button id='recCancel'>Cancel</button>
      <button id='recOnce' style='margin-left: 10px;'>Record</button>
      <button id='recSeries' style='margin-left: 10px;'>Series</button>
     </div>
    </dialog>
    </div>
    </div>
   </div>
  </div>
<script>
  window.onload = function() {
    drawCursor($refresh);
  };
  var globalResizeTimer = null;
  window.onresize = function() {
    if(globalResizeTimer != null) window.clearTimeout(globalResizeTimer);
    globalResizeTimer = window.setTimeout(drawCursor, 200, $refresh);
  };
  function drawCursor(refresh) {
    var now = Date.now()/1000;
    if(now > $tnext) {
	const params = new URLSearchParams(window.location.search);
	params.delete('left.x');
	params.delete('right.x');
	params.delete('eventId');
	var newurl = location.pathname + '?' + params.toString();
	window.history.replaceState({}, '', newurl);
	location.reload(true);
    }
    var cursor = document.getElementById('timenow');
    if(cursor) {
	var elem = document.getElementById('timeline');
	var start = elem.offsetTop;
	cursor.style.top = (start+33) + 'px';
	cursor.style.height = (elem.offsetHeight-start) + 'px';
	var delta = (now%1800)/$textent;
	var pos = elem.offsetLeft + 6 + $ch_width
		+ 0.98*delta*(elem.offsetWidth-$ch_width-6);
	cursor.style.left = pos + 'px';
	cursor.style.visibility = 'visible';
    }
    if(refresh == 1) {
	var sync = (now % 60) * 1000;
	setTimeout(drawCursor, 63000-sync, 1);  // Avoid race
    }
  }
  function formSubmit() {
    document.media.submit();
  }
  function make_timer(event_id, title, is_series) {
    let recDialog = document.getElementById('recDialog');
    let recCancel = document.getElementById('recCancel');
    let recOnce = document.getElementById('recOnce');
    let recSeries = document.getElementById('recSeries');
    recCancel.addEventListener('click', canc);
    recOnce.addEventListener('click', rec);
    document.getElementById('recText').textContent = `Record \"\${title}\"?`;
    if (is_series) {
      recSeries.style.display = 'block';
      recSeries.addEventListener('click', recs);
    }
    else {
	recSeries.style.display = 'none';
    }
    recDialog.showModal();
    function canc() {
      recCancel.removeEventListener('click', canc);
      recOnce.removeEventListener('click', rec);
      recSeries.removeEventListener('click', recs);
      recDialog.close();
    }
    function rec() {
      location.replace(window.location.href + `&eventId=\${event_id}`);
      recOnce.removeEventListener('click', rec);
      recCancel.removeEventListener('click', canc);
      recSeries.removeEventListener('click', recs);
      recDialog.close();
    }
    function recs() {
      location.replace(window.location.href + `&eventId=\${event_id}&series=1`);
      recCancel.removeEventListener('click', canc);
      recOnce.removeEventListener('click', rec);
      recSeries.removeEventListener('click', recs);
      recDialog.close();
    }
  }
</script>
 </body>
</html>";
?>
