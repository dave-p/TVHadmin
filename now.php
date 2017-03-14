<?php
  $page_title = "What's On Now";
  include_once('head.php');
?>
 <div id="layout">
  <div id="prog_summary2">
 <?php
	$chans = get_channels();
	$wday = date('l, j M Y', time());
	$time = date('H:i', time());

	echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" id=\"heading\"><tr><td class=\"col_title\"><h1>What's on at $time</h1></td></tr></table>";

	echo "<table border=0 cellpadding=0 cellspacing=0 class=\"list hilight\" id=\"content\">";
	echo "<tr class=\"newday\"><td colspan=\"4\"><span class=\"date_long\">$wday</span></td></tr>";
	$i = 0;
	foreach($chans as $c) {
		$e = get_epg($c["val"]);
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
		echo "<td class=\"col_duration\">$start - $end";
		echo "<table border=0 cellspacing=0 cellpadding=0 class=\"percent\" title=\"$don min&nbsp;/&nbsp;$dur min\">";
		echo "<tr><td class=\"elapsed\" width=\"$pc%\"><img src=\"images/spacer.gif\" width=1 height=1 alt=\"\" /></td>";
		echo "<td class=\"remaining\"><img src=\"images/spacer.gif\" width=1 height=1 alt=\"\" /></td></tr></table></td>";
		echo "<td class=\"col_channel\"><div class=\"channel_name\">" . $c["val"] . "</div></td>";
		echo "<td class=\"col_title\"><div class=\"epg_title\">" . $p["title"] . "</div><div class=\"epg_subtitle\">" . $p["summary"] . "</div></td>";
		echo "</tr>\n";
		$i++;
	}
	echo "</table></div>\n";
 ?>
   </div>
  </div>
 </body>
</html>
