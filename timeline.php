<?php
	$page_title = "Timeline";
	include_once './head.php';
	$now = time();
	$chans = get_channels();
	$tags = get_channeltags();
	if(isset($settings['TIMESPAN'])) {
		$textent = $settings['TIMESPAN'] * 3600;
	}
	else $textent = 14400;
	if(isset($_GET['start'])) {
		$utime = max($_GET['start'], $now);
		if(isset($_GET['right_x'])) $utime += $textent/2;
		else if(isset($_GET['left_x'])) $utime = max($utime-$textent/2, $now);
	}
	else $utime = $now;
	$toffset = $utime % 1800;	//secs from start of chart to now
	$tstart = $utime - $toffset;
	$tend = $tstart + $textent;
	$tnext = $tend;
	$wday = date('D j M', $utime);
	$media = array();
	$colours = array('#ffffff', '#dee6ee', '#ffffff', '#dee6ee');
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
	foreach ($tags as $v=>$t) {
	  $tt = urlencode($t);
	  if (isset($settings["Tag_$tt"])) {
		echo "
	<div class='media'>
	  <label for='$tt'>$t:</label>
	  <input type='checkbox' name='$tt' id='$tt' onchange='formSubmit()'";
		if (isset($_GET['update'])) {
			if (isset($_GET[$tt])) {
				$media[$t] = 1;
				echo " checked";
			}
		}
		else {
			$g = "Time_" . $tt;
 			if (isset($settings[$g])) {
				$media[$t] = 1;
				echo " checked";
			}
		}
		echo ">
	</div>";
	  }
	}
	echo "
      </td>
      <td>
       <input type='image' alt='Earlier' title='Earlier' name='left' src='images/left.png'>
       <input type='image' alt='Later' title='Later' name='right' src='images/right.png'>
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
     <tr class='newday'>
      <td>$wday</td>
      <td>";
	$t = $tstart;
	for($i=0; $i<4; $i++){
	    $time = date('H:i', $t);
	    echo "
	<div style='float: left; background-color: {$colours[$i]}; width: 24.5%;'>$time</div>";
	    $t += $textent / 4;
	}
	echo "
      </td>
     </tr>";
	if (isset($settings["CSORT"]) && ($settings["CSORT"] == 1)) $lcn = 1;
	else $lcn = 0;
	$i = 0;
	foreach($chans as $c) {
	    if (!isset($media["All"])) {
	        foreach($c["tags"] as $t) {
			if (array_key_exists($tags[$t], $media)) goto good;
                }
                continue;
	    }
good:
	    $e = get_epg($c["name"], $tstart, $tend);
	    if (!isset($e)) continue;
	    $wd = 98 - count($e)/8;
	    echo "
     <tr>
      <td class='col_channel'>
       <div class='channel_name' style='background-color: ";
	    if ($lcn) print "{$colours[$i%2]}'>{$c['number']} {$c['name']}";
	    else print "{$colours[$i%2]}'>{$c['name']}";
	    echo "
       </div>
      </td>
      <td class='col_schedule'>";
	    foreach ($e as $p) {
		if ($p['start'] <= $now && $p['stop'] > $now) {
		    if ($p['start'] > $tstart) {	#Need a spacer
			$spc = (($p['start'] - $tstart) * $wd) / $textent;
			echo "
	 <div class='spacer' style='width: $spc%;'>
	  <img src='images/spacer.gif' width=1 height=1 alt=''>
	 </div>";
		    }
		    $colour = '#b4e29c';
		    $tnext = min($tnext, $p['stop']);
		}
		else $colour = '#dee6ee';
		$duration = min($tend, $p['stop']) - max($tstart, $p['start']);
		if ($duration == 0) continue;
		$pc = ($wd * $duration) / $textent;
		if (isset($p['summary'])) $desc = $p['summary'];
		else $desc = $p['description'];
		@$subtitle = htmlspecialchars($desc, ENT_QUOTES|ENT_HTML5);
		echo "
	 <div class='item' style='background-color: $colour; width: $pc%;' title='$subtitle'>
	    {$p['title']}</div>";
	    }
	    echo "
      </td>
     </tr>";
	    $i++;
	}
	echo "
    </table>";
	if(($now >= $tstart) && ($now < $tend)) echo "
    <span id='timenow'>
     <img src='images/spacer.gif' width='1' height='1' alt=''>
    </span>";
	echo "
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
	var newurl = location.pathname + '?' + params.toString();
	window.history.replaceState({}, '', newurl);
	location.reload(true);
    }
    var cursor = document.getElementById('timenow');
    if(cursor) {
	var elem = document.getElementById('timeline');
	var rect = elem.getBoundingClientRect();
	var start = Math.max(0,6+rect.top);
	cursor.style.top = (start+27) + 'px';
	cursor.style.height = (rect.height-start) + 'px';
	var delta = (now%1800)/$textent;
	var pos = rect.left + 6 + $ch_width
		+ 0.98*delta*(rect.width-$ch_width-6);
	cursor.style.left = pos + 'px';
    }
    if(refresh == 1) {
	var sync = (now % 60) * 1000;
	setTimeout(drawCursor, 63000-sync, 1);  // Avoid race
    }
  }
  function formSubmit() {
    document.media.submit();
  }
</script>
 </body>
</html>";
?>
