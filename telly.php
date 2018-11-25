<?php
  $page_title = 'Channels';
  include_once './head.php';
?>
	<script type="text/javascript">
	function formSubmit()
	{
	document.whatandwhen.submit();
	}
	</script>
 <?php
        $timers = get_timers();
	$tevents = array();
	foreach ($timers as $t) {
	  $tevents[$t["broadcast"]] = 1;
	}
	$links = get_links();
	$levents = array();
	foreach ($links as $l) {
	  $levents[$l["serieslink"]] = 1;
	}
        $dt = localtime(time(), true);
        $today = mktime($epg_start, 0, 0, $dt["tm_mon"]+1, $dt["tm_mday"], $dt["tm_year"]+1900);
	if(isset($_GET['prog'])) {
	  $prog = $_GET['prog'];
	  $uprog = urlencode($prog);
	}
	if(isset($_GET['when'])) {
	  $when = $_GET['when'];
	}
	else {
	  $when = $today;
	}
	$id = 0;

        echo "
  <div id='layout'>
    <div id='banner'>
      <form name='whatandwhen' method='GET' action='telly.php'>
        <table>
	  <tr>
	    <td class='col_title'><div id='mobmenu'>&#9776;</div> <h1>Channels</h1></td>
	    <td>Channel: <select name='prog' size='1' onchange='formSubmit()'>
	      <option value=''>Select Channel</option>
	";
	$chans = get_channels();
	foreach($chans as $v) {
	  $cname = $v["name"];
	  print("<option value='$cname'");
	  if (isset($prog) && $cname == $prog) {
	    print (" selected");
	  }
	  print(">$cname</option>");
	}

        if (isset($_GET['all'])) $all = 'checked';
        else $all = '';
        echo "
	    </select>
	  </td>
	  <td>
            <label>
              All Dates <input type='checkbox' name='all' $all onchange='formSubmit()'>
            </label>";
	if ($all !== 'checked') {
	  $prev = $when - 86400;
	  if ($prev >= $today) {
	    echo "<a href='telly.php?prog=$uprog&when=$prev'><img src='images/left.png'></a>";
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
	  echo "</select>&nbsp;";
	  $next = $when + 86400;
	  if (isset($prog) && ($next < $date)) {
	    echo "<a href='telly.php?prog=$uprog&when=$next'><img src='images/right.png'></a>";
	  }
	}
	else $next = 0;
	echo "
	    </td>
	  </tr>
        </table>
      </form>
    </div>
    <div id='wrapper'>
      <div id='content'>";

	if(isset($prog) && ($prog !== '')) {
	  echo "
	<table class='list'>
	  <tr class='heading'>
	   <td colspan='4'><span class='channel_name'>$prog</span></td>
	  </tr>";
	  $progs = get_epg($prog, $when, $next);
	  $i = 0;
	  $last_prog_date = " ";
	  foreach($progs as $p) {
	    if (isset($_GET['all'])) {
	      $d = date('l d/n', $p["start"]);
	      if ($d != $last_prog_date) {
		echo "<tr class='newday'><td colspan='5'><span class='date_long'>$d</span></td></tr>";
		$last_prog_date = $d;
	      }
	    }
	    $start = date('H:i', $p["start"]);
	    $end = date('H:i', $p["stop"]);
	    if ($i % 2) {
	      echo "<tr class='row_odd' id='$id'>";
	    }
	    else {
	      echo "<tr class='row_even' id='$id'>";
	    }
	    $id++;
	    print("<td class='col_duration'>$start - $end</td>");
	    printf("<td class='col_title'><div class='epg_title'>%s</div><div class='epg_subtitle'>%s</div></td>", $p["title"],$p[$settings['SUMM']]);
            $evt = $p["eventId"];
	    if (!array_key_exists($evt, $tevents)) {
	      echo "<td><a href='record.php?eventId=$evt&series=N&from=2&id=$id&when=$when&prog=$uprog'><img src='images/rec_button1.png' alt='record' title='record'></a></td>";
	    }
	    else {
	      echo "<td></td>";
	    }
	    if ((isset($p["serieslinkUri"])) && !array_key_exists($p["serieslinkUri"], $levents)) {
	      echo "<td><a href='record.php?eventId=$evt&series=Y&from=2&id=$id&when=$when&prog=$uprog'><img src='images/rec_buttonS.png' alt='record series' title='record series'></a></td></tr>";
	    }
	    else {
	      echo "<td></td></tr>";
            }
	    $i++;
	  }
	  echo "</table></div>\n";
	}
 ?>
      </div>
    </div>
  </div>
</body>
</html>
