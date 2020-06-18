<?php
  $page_title = 'Channels';
  include_once './head.php';
?>
	<script>
	function formSubmit()
	{
	document.whatandwhen.submit();
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
	if (isset($settings["CSORT"]) && ($settings["CSORT"] == 1)) $lcn = 1;
	else $lcn = 0;
	foreach($chans as $v) {
	  $cname = $v["name"];
	  print("<option value='$cname'");
	  if (isset($prog) && $cname == $prog) {
	    $prog_lcn = $v["number"];
	    print (" selected");
	  }
	  if ($lcn) print(">{$v["number"]} $cname</option>");
	  else print(">$cname</option>");
	}

        if (isset($_GET['all'])) {
	    $all = 'checked';
	    $rall = '&all=on';
	}
        else {
	    $all = '';
	    $rall = '';
	}
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
	   <td colspan='4'>";
	if (isset($prog_lcn)) print "<span class='channel_name'>$prog_lcn $prog</span>";
	else print "<span class='channel_name'>$prog</span>";
	echo "
	    </td>
	  </tr>";
	  $progs = get_epg($prog, $when, $next);
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
	    echo "<tr class='row_alt' id='$id'><td class='col_duration'>$start - $end</td>";
	    printf("<td class='col_title'><div class='epg_title'>%s</div><div class='epg_subtitle'>%s</div></td>", $p["title"],$p[$settings['SUMM']]);
	    if (!isset($p['dvrState']) || ($p['dvrState'] != 'scheduled' && $p['dvrState'] != 'recording')) {
	      $evt = $p["eventId"];
	      echo "<td><a href='telly.php?eventId=$evt&series=N&when=$when&prog=$uprog$rall#$id'><img src='images/rec_button1.png' alt='record' title='record'></a></td>";
	    }
	    else {
	      echo "<td></td>";
	    }
	    if ((isset($p["serieslinkUri"])) && !array_key_exists($p["serieslinkUri"], $levents)) {
	      echo "<td><a href='telly.php?eventId=$evt&series=Y&when=$when&prog=$uprog$rall#$id'><img src='images/rec_buttonS.png' alt='record series' title='record series'></a></td></tr>";
	    }
	    else {
	      echo "<td></td></tr>";
            }
	  }
	  echo "</table></div>\n";
	}
 ?>
      </div>
    </div>
  </div>
</body>
</html>
