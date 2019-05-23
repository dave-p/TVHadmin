<?php
  $page_title = 'Series Links';
  include_once './head.php';
  if (isset($_GET["uuid"])) {
    $uuid = $_GET["uuid"];
    $url = "$urlp/api/idnode/delete?uuid=$uuid";
    file_get_contents($url);
  }
  echo "
    <div id='layout'>
     <div id='banner'>
      <table>
        <tr>
	  <td class='col_title'><div id='mobmenu'>&#9776;</div> <h1>Series Links</h1></td>
	</tr>
      </table>
     </div>
     <div id='wrapper'>
      <div id='content'>
       <table class='list'>
	<tr class='heading'>
	  <td class='col_value'><h2>Timers</h2></td>
	  <td class='col_value'><h2>Recs</h2></td>
	  <td class='col_channel'><h2>Channel</h2></td>
	  <td class='col_name'><h2>Name</h2></td>
	  <td class='wideonly col_channel'><h2>Link</h2></td>
	  <td class='col_delete'></td>
	</tr>
  ";
        $links = get_links();
	$channels = get_channels();
	$timers = get_timers();
	$recordings = get_recordings(-1);
	$i = 0;
	foreach($links as $l) {
	    if ($i % 2) {
		echo "<tr class='row_odd'>";
	    }
	    else {
		echo "<tr class='row_even'>";
	    }
	    $lc = $l["channel"];
	    foreach($channels as $c) {
		if ($lc === $c["uuid"]) {
		    $channelname = $c["name"];
		    break;
		}
	    }
	    $n = 0;
	    foreach($timers as $t) {
		if ($t["autorec"] === $l["uuid"]) $n++;
	    }
	    $recs = 0;
	    foreach($recordings as $r) {
		if ($r["sched_status"] == "scheduled") continue;
		if ((isset($r['autorec'])) && ($l['uuid'] === $r['autorec'])) $recs++;
	    }
	    $l['title'] = stripslashes($l['title']);
	    $crid = substr(strstr($l['serieslink'], '//'), 2);
	    echo "
	<td class='col_value'>$n</td>
	<td class='col_value'>$recs</td>
	<td class='col_channel'>$channelname</td>
	<td class='col_name'>{$l['title']}</td>
	<td class='wideonly col_channel'>$crid</td>
	<td class='col_delete'><a href='links.php?uuid={$l['uuid']}'><img src='images\delete.png'></a></td>
      </tr>\n";
	    $i++;
	}
 ?>
     </table>
    </div>
   </div>
  </div>
 </div>
</body>
</html>
