<?php
	$page_title = "What's On Now";
	include_once './head.php';
	$chans = get_channels();
	$tags = get_channeltags();
	$tag = array();
	foreach ($tags as $t) {
		$tag[$t["key"]] = $t["val"];
	}
	$wday = date('l, j M Y', time());
	$time = date('H:i', time());
	$media = array();
	if (array_key_exists('NOANON', $settings)) {
		$view_url = $urlp;
	}
	else {
		$view_url = 'http://' . $ip;
	}
	echo "
	<script type='text/javascript'>
	function formSubmit()
	{
	document.media.submit();
	}
	</script>
 <div id='layout'>
  <div id='banner'>
   <form name='media' method='GET' action='now.php'>
    <input type='hidden' name='update' value='1'>
    <table>
     <tr>
      <td class='col_title'><div id='mobmenu'>&#9776;</div> <h1>What's on at $time</h1></td>
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
			$g = "Media_" . $t;
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
   <div id='whatson'>
    <table class='list'>
     <tr class='newday'>
      <td colspan='4'><span class='date_long'>$wday</span></td>
     </tr>";
	$i = 0;
	foreach($chans as $c) {
		foreach($c["tags"] as $t) {
			if (array_key_exists($tag[$t], $media)) goto good;
		}
		continue;
good:
		$e = get_epg_now($c["name"]);
		$p = &$e[0];
		if ($i % 2) echo "<tr class=\"row_odd\">";
		else echo "<tr class=\"row_even\">";
		$start = date('H:i', $p["start"]);
		$end = date('H:i', $p["stop"]);
		$duration = $p["stop"] - $p["start"];
		if($duration > 0) $pc = intval(100*(time() - $p["start"])/$duration);
		else $pc = 0;
		$dur = intval($duration/60);
		$don = intval((time() - $p["start"])/60);
		if (isset($p['summary'])) $summ = $p['summary'];
		else $summ = '';
		echo "
      <td class='col_duration'>$start - $end
       <table border=0 cellspacing=0 cellpadding=0 class='percent' title='$don min&nbsp;/&nbsp;$dur min'>
	<tr>
	 <td class='elapsed' width='$pc%'><img src='images/spacer.gif' width=1 height=1 alt=''></td>
	 <td class='remaining'><img src='images/spacer.gif' width=1 height=1 alt=''></td>
	</tr>
       </table>
      </td>
      <td class='col_channel'>
       <div class='channel_name'>{$c['name']}</div>
      </td>
      <td class='col_title'>
       <div class='epg_title'>{$p['title']}</div>
       <div class='epg_subtitle'>{$summ}</div>
      </td>
      <td class='col_stream'>
	<a href='$view_url/play/stream/channel/{$c['uuid']}?title={$c['name']}'><img src='images\play.png' title='Play'></a>
      </td>
     </tr>";
		$i++;
	}
	echo "</table></div>\n";
 ?>
    </div>
   </div>
  </div>
 </body>
</html>
