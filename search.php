<?php
  $page_title = 'Search Results';
  include_once('head.php');
?>
	<script type="text/javascript">
	function formSubmit()
	{
	document.forms.telly.submit()
	}
	</script>
  <div id="layout">
    <div id="prog_list">
 <?php
	if($_POST['find'] != "") {
		$find = $_POST["find"];
		echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" id=\"heading\">";
		echo "<tr><td class=\"col_title\"><h1>Matches for: <i>$find</i></h1></td></tr></table>";
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"list hilight\" id=\"content\">";
		$last_prog_date = 'unknown';
#		$query = "SELECT date_format(prog.time,'%a %e %M'), hour(prog.time),minute(prog.time),prog.title,prog.descr,prog.critem,prog.crseries,chan.name FROM prog,chan";
		$i = 0;
		$last_prog_date = " ";
		$results = search_epg("", $find);
		foreach ($results as $r) {
			$d = date('l d/n', $r["start"]);
                        $t = date('H:i', $r["start"]);
			if ($d != $last_prog_date) {
				echo "<tr class=\"newday\"><td colspan=\"4\"><span class=\"date_long\">$d</span></td></tr>";	
				$last_prog_date = $d;
			}
			if ($i % 2) {
				echo "<tr class=\"row_odd\">";
			}
			else {
				echo "<tr class=\"row_even\">";
			}
			echo "<td class=\"col_duration\"><span class=\"time_duration\"><span class=\"time_start\">";
			printf("%s</span></span></td><td class=\"col_channel\"><div class=\"channel_name\">%s</div></td>", $t,$r["channelName"]);
			printf("<td class=\"col_center\"><div class=\"epg_title\">%s</div><div class=\"epg_subtitle\">%s</div></td>", $r["title"],$r["subtitle"]);
			printf("</tr>\n");
			$i++;
		}
		echo "</table>";
	}
?>
    </div>
   </div>
  </div>
  </body>
</html>
