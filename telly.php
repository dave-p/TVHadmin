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
        $dt = localtime(time(), true);
        $date = mktime($epg_start, 0, 0, $dt["tm_mon"]+1, $dt["tm_mday"], $dt["tm_year"]+1900);
	if(isset($_GET['prog'])) {
	  $prog = $_GET['prog'];
	  $when = $_GET['when'];
	}
	else {
	  $when = $date;
	}
	$id = 0;

        echo "
  <div id='layout'>
    <form name='whatandwhen' method='GET' action='telly.php'>
      <table width='100%' border='0' cellspacing='0' cellpadding='0' id='heading'>
	<tr>
	  <td class='col_title'><h1>Channels</h1></td>
	  <td>Channel: <select name='prog' size='1' onchange='formSubmit()'>
	";
	$chans = get_channels();
	foreach($chans as $v) {
	  $cname = $v["val"];
	  print("<option value=\"$cname\"");
	  if (isset($prog) && $cname == $prog) {
	    print (" selected");
	  }
	  print(">$cname</option>");
	}

	echo "</select></td>";
	echo "<td>Date: <select name=\"when\" size=\"1\" onchange=\"formSubmit()\">";

	for($i=0; $i<8; $i++) {
	  $d = date('D d/n', $date);
	  print("<option value=\"$date\"");
	  if (isset($when) && ($date == $when)) {
	    print (" selected");
	  }
	  print(">$d</option>");
	  $date += 86400; 
	}
	echo "</select></td>";

	if(isset($prog)) {
	  echo "<table border=0 cellpadding=2 class=\"list hilight\">";
	  echo "<tr class=\"heading\"><td colspan=\"4\"><span class=\"channel_name\">$prog</span>";
	  echo "</td></tr>";
	  $progs = get_epg($prog);
	  $i = 0;
	  foreach($progs as $p) {
	    $delta = $p["start"] - $when;
	    if (($delta < 0) || ($delta > 86400)) continue;
	    $start = date('H:i', $p["start"]);
	    $end = date('H:i', $p["stop"]);
	    if ($i % 2) {
	      echo "<tr class=\"row_odd\" id=\"$id\">";
	    }
	    else {
	      echo "<tr class=\"row_even\" id=\"$id\">";
	    }
	    $id++;
	    print("<td class=\"col_duration\">$start - $end</td>");
	    printf("<td class=\"col_title\"><div class=\"epg_title\">%s</div><div class=\"epg_subtitle\">%s</div></td>", $p["title"],$p["summary"]);
            $evt = $p["eventId"];
            $dup = 0;
            foreach($timers as $t) {
              if ($evt == $t["broadcast"]) {
                $dup = 1;
                break;
              }
            }
            if ($dup == 0) {
              echo "<td><a href=\"record.php?eventId=$evt&series=N&from=0&id=$id&when=$when&prog=$prog\"><img src=\"images/rec_button1.png\" alt=\"record\" title=\"record\"></a></td>";
              if (isset($p["serieslinkUri"])) {
                echo "<td><a href=\"record.php?eventId=$evt&series=Y&from=0&id=$id&when=$when&prog=$prog\"><img src=\"images/rec_buttonS.png\" alt=\"record series\" title=\"record series\"></a></td>";
              }
            }
            else {
              echo "<td></td></tr>\n";
            }
	    $i++;
	  }
	  echo "</table></div>\n";
	}
 ?>
<!-- end container -->
</div>
</body>
</html>
