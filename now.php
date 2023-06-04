<?php
	$page_title = "What's On Now";
	include_once './head.php';
	$query = $_SERVER['QUERY_STRING'];
	if (isset($_GET["eventId"])) {
	  $evt = $_GET["eventId"];
	  $url = "$urlp/api/dvr/entry/create_by_event?event_id=$evt&config_uuid=$config_uuid";
	  file_get_contents($url);
	  if (preg_match("/eventId=.*?&/", $query)) {
	    $query = preg_replace("/eventId=.*?&/", "", $query);
	  }
	  else $query = "";
	}
	if ($query !== '') $query = '&' . $query;
	$chans = get_channels();
	$tags = get_channeltags();
	$wday = date('l, j M Y', time());
	$time = date('H:i', time());
	if (isset($settings['REFR'])) $timestr = "";
	else $timestr = "at $time";
	$media = array();
	if (array_key_exists('NOANON', $settings)) $view_url = "http://$user:$pass@$ip";
	else $view_url = $urlp;
	if (isset($settings["CSORT"]) && ($settings["CSORT"] == 1)) $lcn = 1;
	else $lcn = 0;

	echo "
<script>
  function formSubmit() {
    document.media.submit();
  }
  window.onload = function() {
	";
	if (isset($settings['REFR'])) echo "setInterval(updateTimeline, 60000); ";
	echo "
    updateTimeline()
  };
  function updateTimeline() {
    var now = Date.now()/1000;
    const bars = document.querySelectorAll('.percent');
    for (const bar of bars) {
      const done = bar.querySelector('.elapsed');
      var start = parseInt(done.dataset.start);
      var stop = parseInt(done.dataset.stop);
      var duration = stop - start;
      var elapsed = now - start;
      var pc = 0;
      if (stop > start) pc = 100*elapsed/duration;
      if (pc > 100) location.reload(true);
      done.style.width = pc + '%';
      bar.title = (Math.round(elapsed/60)) + ' min / ' + (duration/60) + ' min';
    }
  }
</script>
 <div id='layout'>
  <div id='banner'>
   <form name='media' method='GET' action='now.php'>
    <input type='hidden' name='update' value='1'>
    <table>
     <tr>
      <td class='col_title'>
	<div id='mobmenu'>&#9776;</div> <h1>What's On $timestr</h1>
      </td>
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
			$g = "Media_" . $tt;
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
     </tr>
    </table>
   </form>
  </div>
  <div id='wrapper'>
   <div id='whatson'>
    <table class='list'>
     <tr class='newday'>
      <td colspan='5'><span class='date_long'>$wday</span></td>
     </tr>";
	foreach($chans as $c) {
		if (!isset($media["All"])) {
			if (count(array_intersect($c["tags"], $media)) == 0) continue;
		}
		$e = get_epg_now($c["uuid"]);
		$p = &$e[0];
		if ($p) {
			$start = date('H:i', $p["start"]);
			$end = date('H:i', $p["stop"]);
			if (isset($p['summary'])) $summ = $p['summary'];
			else if (isset($p['description'])) $summ = $p['description'];
			else $summ = '';
			echo "
    <tr class='row_alt'>
      <td class='col_duration'>$start - $end
       <table class='percent'>
	<tr>
	 <td class='elapsed' data-start='{$p["start"]}' data-stop={$p["stop"]}><img src='images/spacer.gif' width=1 height=1 alt=''></td>
	 <td class='remaining'><img src='images/spacer.gif' width=1 height=1 alt=''></td>
	</tr>
       </table>
      </td>";
		}
		else {
			echo "
    <tr class='row_alt'>
      <td class='col_duration'></td>";
		}
		echo "
      <td class='col_channel'>
       <div class='channel_name'>";
		if (isset($settings['ICONS']) && isset($c['icon_public_url'])) {
			print "<img src=\"icon.php?image=$urlp/{$c['icon_public_url']}&auth=$auth\" height='48' width='80' alt=\"{$c['name']}\" title=\"{$c['name']}\">";
		}
		else if ($lcn) print "{$c['number']} {$c['name']}";
		else print "{$c['name']}";
		echo "
       </div>
      </td>
      <td class='col_title'>";
		if ($p) {
			echo "
       <div class='epg_title'>{$p['title']}</div>
       <div class='epg_subtitle'>{$summ}</div>
      </td>
      <td>";
			if (isset($p['dvrState']) && $p['dvrState'] == 'recording') {
				echo "
	<img src='images/rec.png' alt='Recording' title='Recording'>";
			}
			else {
				echo "
        <a href='now.php?eventId={$p["eventId"]}$query'><img src='images/rec_button1.png' alt='record' title='record'></a>";
			}
		}
		else echo "
        No EPG available
      </td>
      <td>";
		if (isset($config_suuid)) $s = "&profile={$config_suuid}";
		else $s = "";
		echo "
      </td>
      <td class='col_stream'>
	<a href='$view_url/play/ticket/stream/channel/{$c['uuid']}?title={$c['name']}$s' download='{$c['uuid']}.m3u'><img src='images/play.png' title='Play'></a>
      </td>
     </tr>";
	}
	echo "</table></div>\n";
 ?>
    </div>
   </div>
  </div>
 </body>
</html>
