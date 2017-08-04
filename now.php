<?php
	$page_title = "What's On Now";
	include_once './head.php';
	$chans = get_channels();
	$tags = get_tags();
	$wday = date('l, j M Y', time());
	$time = date('H:i', time());
	$media = array();
	echo "
 <div id='topmenu'>
  <form name='media' method='GET' action='now.php'>
   <input type='hidden' name='update' value='1'>
   <table width='100%' border='0' cellspacing='0' cellpadding='0' id='heading'>
    <tr>
     <td class='col_title'><h1>What's on at $time</h1></td>
     <td>";
	foreach ($tags as $t) {
		$v = $t['val'];
		echo "$v: <input type='checkbox' name='$v'";
		if (isset($_GET['update'])) {
			$g = str_replace(' ','_',$v);
 			if (isset($_GET[$g])) {
				$media[$t['key']] = 1;
				echo " checked";
			}
		}
		else {
			$g = "Media_" . str_replace(' ','_',$v);
 			if (isset($settings[$g])) {
				$media[$t['key']] = 1;
				echo " checked";
			}
		}
		echo "> ";
	}
	echo "
      <input type='submit' value='Update'>
     </td>
    </tr>
   </table>
  </form>
 </div>
 <div id='layout'>
  <div id='prog_summary2'>
   <table border=0 cellpadding=0 cellspacing=0 class='list hilight' id='content'>
    <tr class='newday'>
     <td colspan='4'><span class='date_long'>$wday</span></td>
    </tr>";
	$i = 0;
	foreach($chans as $c) {
		$ct = $c["tags"];
		foreach($ct as $z) {
			if (array_key_exists($z, $media)) goto good;
		}
		goto nogood;
good:
		$e = get_epg($c["name"]);
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
		echo "
      <td class='col_duration'>$start - $end
       <table border=0 cellspacing=0 cellpadding=0 class='percent' title='$don min&nbsp;/&nbsp;$dur min'>
	<tr>
	 <td class='elapsed' width='$pc%'><img src='images/spacer.gif' width=1 height=1 alt='' /></td>
	 <td class='remaining'><img src='images/spacer.gif' width=1 height=1 alt='' /></td>
	</tr>
       </table>
      </td>
      <td class='col_channel'>
       <div class='channel_name'>{$c['name']}</div>
      </td>
      <td class='col_title'>
       <div class='epg_title'>{$p['title']}</div>
       <div class='epg_subtitle'>{$p['summary']}</div>
      </td>
     </tr>";
		$i++;
nogood:
	}
	echo "</table></div>\n";
 ?>
   </div>
  </div>
 </body>
</html>
