<?php
	$page_title = "Timeline";
	include_once './head.php';
	$chans = get_channels();
	$tags = get_channeltags();
	$tag = array();
	foreach ($tags as $t) {
		$tag[$t["key"]] = $t["val"];
	}
	$utime = time();
	$wday = date('D j M', $utime);
	$media = array();
	$colours = array('#ffffff', '#dee6ee', '#ffffff', '#dee6ee');
	if(isset($settings['TIMESPAN'])) {
		$textent = $settings['TIMESPAN'] * 3600;
	}
	else $textent = 14400;
	$toffset = $utime % 1800;	//secs from start of chart to now
	$tstart = $utime - $toffset;
	$tend = $tstart + $textent;
	$now = ($utime - $tstart) / $textent;
	echo "
	<script type='text/javascript'>
	window.onload = drawCursor;

	var globalResizeTimer = null;
	window.onresize = function() {
	    if(globalResizeTimer != null) window.clearTimeout(globalResizeTimer);
	    globalResizeTimer = window.setTimeout(drawCursor(), 200);
	};
	function drawCursor() {
	    var elem = document.getElementById('schedules');
	    if(elem) {
		var rect = elem.getBoundingClientRect();
		var cursor = document.getElementById('timenow');
		cursor.style.top = (rect.top+22) + 'px';
		cursor.style.height = (rect.height-22) + 'px';
		var pos = rect.left + 0.98*$now*rect.width;
		cursor.style.left = pos + 'px';
	    }
	}
	function formSubmit() {
	    document.media.submit();
	}
	</script>
 <div id='layout'>
  <div id='banner'>
   <form name='media' method='GET' action='timeline.php'>
    <input type='hidden' name='update' value='1'>
    <table>
     <tr>
      <td class='col_title'><div id='mobmenu'>&#9776;</div> <h1>Timeline</h1></td>
      <td>";
	foreach ($tag as $v=>$t) {
	  if (isset($settings["Tag_$t"])) {
		$tt = urlencode($t);
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
			$g = "Time_" . $t;
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
     </tr>
    </table>
   </form>
  </div>
  <div id='wrapper'>
   <div id='timeline'>
    <table class='list' style='table-layout: fixed;'>
     <col width=120px>
     <col id='schedules' width=100%>
     <tr class='newday'>
      <td>$wday</td>
      <td>";
	$t = $tstart;
	for($i=0; $i<4; $i++){
	    $time = date('H:i', $t);
	    echo "
	 <div class='elapsed' style='float: left; background-color: {$colours[$i]}; width: 24.5%;'>
	    <img src='images/spacer.gif' width=1 height=1 alt=''>$time</div>";
	    $t += $textent / 4;
	}
	echo "
      </td>
     </tr>";
	$i = 0;
	foreach($chans as $c) {
	        foreach($c["tags"] as $t) {
			if (array_key_exists($tag[$t], $media)) goto good;
                }
                continue;
good:
		$e = get_epg($c["name"], $tstart, $tend);
		$wd = 98 - count($e)/8;
		echo "
     <tr>
      <td class='col_channel'>
       <div class='channel_name' style='background-color: {$colours[$i%2]}'>{$c['name']}</div>
      </td>
      <td class='col_schedule'>";
		$p = &$e[0];
		$pcount = 0;
		while (isset($p['start'])) {
		    if ($pcount == 0) {
			if ($p['start'] > $tstart) {	#Need a spacer
			    $spc = (($p['start'] - $tstart) * $wd) / $textent;
			    echo "
	 <div class='spacer' style='width: $spc%;'>
	    <img src='images/spacer.gif' width=1 height=1 alt=''></div>";
			}
			$colour = '#b4e29c';
		    }
		    else $colour = '#dee6ee';
		    $duration = min($tend, $p['stop']) - max($tstart, $p['start']);
		    $pc = ($wd * $duration) / $textent;
		    @$subtitle = $p[$settings['SUMM']];
		    echo "
	 <div class='item' style='background-color: $colour; width: $pc%;' title=\"$subtitle\">
	    <img src='images/spacer.gif' width=1 height=1 alt=''>{$p['title']}</div>";
		    $pcount++;
		    $p = &$e[$pcount];
		}
		echo "
      </td>
     </tr>";
		$i++;
	}
	echo "
    </table>
    <span id='timenow'>
     <img src='images/spacer.gif' border='0' width='1' height='1'>
    </span>
    </div>\n";
 ?>
    </div>
   </div>
  </div>
 </body>
</html>
