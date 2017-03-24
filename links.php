<?php
  $page_title = 'Series Links';
  include_once './head.php';
?>
  <div id="series_list">
    <div id="layout">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="heading"><tr><td class="col_title"><h1>Series Links</h1></td></tr></table>
      <table width="100%" border=0 cellpadding=0 class="list hilight"><tr class="heading">
      <td class="col_start"><h2>Timers</h2></td>
      <td class="col_channel"><h2>Channel</h2></td>
      <td class="col_name"><h2>Name</h2></td> 
      <td class="col_delete"></td></tr>
<?php
        $links = get_links();
	$channels = get_channels();
	$timers = get_timers();
	$i = 0;
	foreach($links as $l) {
	    if ($i % 2) {
		echo "<tr class=\"row_odd\">";
	    }
	    else {
		echo "<tr class=\"row_even\">";
	    }
	    $lc = $l["channel"];
	    foreach($channels as $c) {
		if ($lc == $c["key"]) {
		    $channelname = $c["val"];
		    break;
		}
	    }
	    $n = 0;
	    foreach($timers as $t) {
		if ($t["autorec"] == $l["uuid"]) $n++;
	    }
            printf("<td class=\"col_start\"><div>%d</div></td>", $n);
	    printf("<td class=\"col_channel\"><div>%s</div></td>", $channelname);
            printf("<td class=\"col_name\"><div>%s</div></td>", $l["title"]);
            printf ("<td class=\"col_delete\"><a href=\"delete.php?uuid=%s\"><img src=\"images\delete.png\"></a></td></tr>\n", $l["uuid"]);
	    $i++;
	}
	echo "</table></div>\n";
 ?>
   </div>
  </div>
  </body>
</html>
