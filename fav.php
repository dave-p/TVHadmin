<?php
  $page_title = 'Favourite Channels';
  include_once './head.php';
?>
	<script>
	function formSubmit()
	{
	document.whichday.submit();
	}
	</script>
 <?php
	if (isset($_GET["eventId"])) {
	  $evt = $_GET["eventId"];
	  if ($_GET["series"] == 'Y') {
	    $url = "$urlp/api/dvr/autorec/create_by_series?event_id=$evt&config_uuid=$config_uuid";
	  }
	  else {
	    $url = "$urlp/api/dvr/entry/create_by_event?event_id=$evt&config_uuid=$config_uuid";
	  }
	  file_get_contents($url);
	}
        $choices = &$settings['selected_channels'];
	$links = get_links();
	$levents = array();
	foreach ($links as $l) {
	  $levents[$l["serieslink"]] = 1;
	}
	$channels = get_channels();
	$channel_names = array_column($channels, 'name');
        $dt = localtime(time(), true);
        $today = mktime($epg_start, 0, 0, $dt["tm_mon"]+1, $dt["tm_mday"], $dt["tm_year"]+1900);
	if(isset($_GET['when'])) {
	  $when = $_GET['when'];
	}
	else {
	  $when = $today;
	}

        echo "
    <div id='layout'>
      <div id='banner'>
	<form name='whichday' method='GET' action='fav.php'>
	  <table>
	    <tr>
	      <td class='col_title'>
		<div id='mobmenu' title='menu'>&#9776;</div> <h1>Favourite Channels</h1>
	      </td>
	      <td>";
	$prev = $when - 86400;
	if ($prev >= $today) {
	  echo "<a href='fav.php?when=$prev'><img src='images/left.png'></a>";
        }
	echo "&nbsp;<select name='when' size='1' onchange='formSubmit()'>";
	$date = $today;
	for($i=0; $i<8; $i++) {
	  $d = date('D d/n', $date);
	  print("<option value='$date'");
	  if (isset($when) && ($date == $when)) {
	    print (" selected");
	  }
	  print(">$d</option>");
	  $date += 86400; 
	}
	echo "</select>&nbsp; ";
	$next = $when + 86400;
	if ($next < $date) {
	  echo "<a href='fav.php?when=$next'><img src='images/right.png'></a>";
	}
	echo "
	      </td>
	    </tr>
	  </table>
	</form>
      </div>
      <div id='wrapper'>
	<div id='content'>";
	$id = 0;

	foreach ($choices as $c) {
	  if (array_search($c, $channel_names) === false) continue;
	  echo "<table class='list'>";
	  echo "<tr class='heading'><td colspan='4'><span class='channel_name'>$c</span>";
	  echo "</td></tr>";
	  $progs = get_epg($c, $when, $next);

	  foreach($progs as $p) {
	    $start = date('H:i', $p["start"]);
	    $end = date('H:i', $p["stop"]);
	    if (isset($p['summary'])) $desc = $p['summary'];
	    else $desc = $p['description'];
	    echo "<tr class='row_alt' id='$id'><td class='col_duration'>$start - $end</td>";
	    printf("<td class='col_title'><div class='epg_title'>%s</div><div class='epg_subtitle'>%s</div></td>", $p["title"], $desc);
	    if (isset($p['dvrState']) && ($p['dvrState'] == 'scheduled' || $p['dvrState'] == 'recording')) {
	      if (isset($p["serieslinkUri"]) && array_key_exists($p["serieslinkUri"], $levents)) {
		echo "<td></td><td><img src='images/rec.png' title='Series recording scheduled'></td>";
	      }
	      else {
	        echo "<td><img src='images/rec.png' title='Recording scheduled'></td>";
	      }
	    }
	    else {
	      echo "<td><a href='fav.php?eventId={$p['eventId']}&series=N&when=$when#$id'><img src='images/rec_button1.png' title='record'></a></td>";
	      if (isset($p["serieslinkUri"])) {
		echo "<td><a href='fav.php?eventId={$p['eventId']}&series=Y&when=$when#$id'><img src='images/rec_buttonS.png' title='record series'></a></td></tr>";
	      }
	      else {
		echo "<td></td></tr>";
	      }
	    }
	    $id++;
	  }
	  echo "</table>";
	}
?>
	</div>
      </div>
    </div>
<!-- end container -->
  </div>
</body>
</html>

