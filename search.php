<?php
  $page_title = 'Search Results';
  include_once './head.php';
?>
	<script>
	function formSubmit()
	{
	document.forms.telly.submit()
	}
	</script>
  <div id="layout">
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
	$find = $_GET["find"];
	$links = get_links();
	$levents = array();
	foreach ($links as $l) {
		$levents[$l["serieslink"]] = 1;
	}
	echo "
	<div id='banner'>
	  <table>
	    <tr>
	      <td class='col_title'>
	        <div id='mobmenu'>&#9776;</div> <h1>Matches for: <i>$find</i></h1>
	      </td>
	    </tr>
	  </table>
	</div>
	<div id='wrapper'>
	  <div id='content'>
	    <table class='list'>";
	$last_prog_date = " ";
	$results = search_epg("", $find);
	foreach ($results as $r) {
		$d = date('l d/n', $r["start"]);
		$t = date('H:i', $r["start"]);
		$e = date('H:i', $r["stop"]);
		if (isset($r['summary'])) $desc = $r['summary'];
		else $desc = $r['description'];
		if ($d != $last_prog_date) {
			echo "<tr class='newday'><td colspan='5'><span class='date_long'>$d</span></td></tr>";
			$last_prog_date = $d;
		}
		echo "
	      <tr class='row_alt'>
		<td class='col_duration'>
		  <span class='time_duration'>$t - $e</span></td>
		<td class='col_channel'>
		  <div class='channel_name'>{$r['channelName']}</div></td>
		<td class='col_center'>
		  <div class='epg_title'>{$r['title']}</div><div class='epg_subtitle'>{$desc}</div></td>";
		if (isset($r['dvrState']) && ($r['dvrState'] == 'scheduled' || $r['dvrState'] == 'recording')) {
		  if (isset($r["serieslinkUri"]) && array_key_exists($r["serieslinkUri"], $levents)) {
		    echo "<td></td><td><img src='images/rec.png' title='Series recording scheduled'></td>";
		  }
		  else {
		    echo "<td><img src='images/rec.png' title='Recording scheduled'></td>";
		  }
		}
		else {
		  echo "<td><a href='search.php?eventId={$r['eventId']}&series=N&find={$find}'><img src='images/rec_button1.png' title='record'></a></td>";
		  if (isset($r["serieslinkUri"])) {
		    echo "<td><a href='search.php?eventId={$r['eventId']}&series=Y&find={$find}'><img src='images/rec_buttonS.png' title='record series'></a></td></tr>";
		  }
		  else {
		    echo "<td></td></tr>";
		  }
		}
	}
?>
	    </table>
	  </div>
	</div>
      </div>
    </div>
  </body>
</html>
