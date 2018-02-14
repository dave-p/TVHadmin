<?php
  $page_title = 'Favourite Channels';
  include_once './head.php';
?>
	<script type="text/javascript">
	function formSubmit()
	{
	document.whichday.submit();
	}
	</script>
 <?php
        $chans = &$settings['selected_channels'];

	$timers = get_timers();
	$tevents = array();
	foreach ($timers as $t) {
	  $tevents[$t["broadcast"]] = 1;
	}
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
	      <form name='whichday' method='GET' action='fav.php'>
		<table id='heading'>
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
	echo "</td></tr></table></form>";
	$id = 0;

	foreach ($chans as $c) {
	  echo "<table class='list'>";
	  echo "<tr class='heading'><td colspan='4'><span class='channel_name'>$c</span>";
	  echo "</td></tr>";
	  $progs = get_epg($c);

	  $i = 0;
	  foreach($progs as $p) {
	    $delta = $p["start"] - $when;
	    if (($delta < 0) || ($delta > 86400)) continue;
	    $start = date('H:i', $p["start"]);
	    $end = date('H:i', $p["stop"]);
	    if ($i % 2) {
	      echo "<tr class='row_odd' id='$id'>";
	    }
	    else {
	      echo "<tr class='row_even' id='$id'>";
	    }
	    print("<td class='col_duration'>$start - $end</td>");
	    printf("<td class='col_title'><div class='epg_title'>%s</div><div class='epg_subtitle'>%s</div></td>", $p["title"],$p[$settings['SUMM']]);
	    $evt = $p["eventId"];
	    if (!array_key_exists($evt, $tevents)) {
	      echo "<td><a href='record.php?eventId=$evt&series=N&from=1&id=$id&when=$when'><img src='images/rec_button1.png' alt='record' title='record'></a></td><td>";
	      if (isset($p["serieslinkUri"])) {
		echo "<a href='record.php?eventId=$evt&series=Y&from=1&id=$id&when=$when'><img src='images/rec_buttonS.png' alt='record series' title='record series'></a>";
	      }
	      echo "</td></tr>\n";
	    }
	    else {
              echo "<td></td><td></td></tr>\n";
	    }
	    $i++;
	    $id++;
	  }
	  echo "</table>";
	}
 ?>
    </div>
<!-- end container -->
</div>
</body>
</html>

