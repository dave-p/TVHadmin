<?php
  $page_title = 'Recordings';
  include_once './head.php';
  $query = $_SERVER['QUERY_STRING'];
  if (isset($_GET["uuid"])) {
    $uuid = $_GET["uuid"];
    $url = "$urlp/api/dvr/entry/remove?uuid=$uuid";
    file_get_contents($url);
    $query = preg_replace("/uuid=.*?&/", "", $query);
  }
  if (isset($_GET['SORT'])) $sort = $_GET['SORT'];
  else if (isset($settings['SORT'])) $sort = $settings['SORT'];
  else $sort = 0;
  $chtags = array();
  $chans = get_channels();
  foreach ($chans as $c) {
    $chtags[$c["uuid"]] = $c["tags"];
  }
  $media = array();
  $tags = get_channeltags();
  if (array_key_exists('NOANON', $settings)) $view_url = "http://$user:$pass@$ip";
  else $view_url = $urlp;
  echo "
    <script>
      function formSubmit(which) {
	var formObject = document.forms[which];
        formObject.submit();
      }
    </script>
    <div id='layout'>
      <div id='banner'>
	<table>
	  <tr>
	      <td class='col_title'><div id='mobmenu'>&#9776;</div> <h1>Recordings</h1>
	      </td>
	      <td>
		<form name='options' method='GET' action='recordings.php'>";
  foreach ($orders as $key=>$value) {
    echo "<div class='media'><label for='B$key'>$value:</label>";
    echo "<input type='radio' name='SORT' id='B$key' value='$key' onChange='formSubmit(\"options\")'";
    if ($key == $sort) echo " checked></div>";
    else echo "></div>";
  }
  echo "
	      </td>
	      <td><span class='wideonly'>
		  <input type='hidden' name='update' value='1'>
	      ";
  foreach ($tags as $t=>$v) {
    $tt = urlencode($t);
    if (isset($settings["Tag_$tt"])) {
      echo "
	<div class='media'>
	  <label for='$tt'>$t:</label>
	  <input type='checkbox' name='$tt' id='$tt' onchange='formSubmit(\"options\")'";
      if (isset($_GET['update'])) {
          if (isset($_GET[$tt])) {
              $media[$t] = $v;
              echo " checked";
           }
      }
      else {
          $g = "Rec_" . $tt;
          if (isset($settings[$g])) {
              $media[$t] = $v;
              echo " checked";
          }
      }
      echo ">
	</div>";
    }
  }
  echo "
		</form>
	      </span>
	    </td>
	  </tr>
	</table>
      </div>
      <div id='wrapper'>
	<div id='content'>
	  <table class='list'>
	    <tr class='heading'>
	      <td class='col_date'><h2>Date</h2></td>
	      <td class='wideonly col_time'><h2>Time</h2></td>
	      <td class='col_channel'><h2>Channel</h2></td>
	      <td class='wideonly col_length'><h2>Length</h2></td>
	      <td class='col_name'><h2>Name</h2></td>
	      <td class='col_delete'></td>
	      <td class='col_stream'></td>
	    </tr>
  ";
        $recordings = get_recordings($sort);
	foreach($recordings as $t) {
		if ($t["sched_status"] == "scheduled") continue;
		if (!isset($media["All"])) {
			$cid = $t["channel"];
			if (array_key_exists($cid, $chtags)) {
				if (count(array_intersect($chtags[$cid], $media)) == 0) continue;
			}
		}
		$time = date("H:i", $t["start"]);
		$date = date("D d/m/y", $t["start"]);
		if (isset($t["uri"]) && strpos($t["uri"], "#")) {
			$duration = $t["stop_real"] - $t["start_real"];
		}
		else $duration = $t["stop"] - $t["start"];
		$hh = $duration / 3600;
		$mm = ($duration % 3600) / 60;
		if ($t['sched_status'] == 'completed') {
			$ok = 1;
			if ($t['status'] == 'Completed OK') {
				echo "<tr class='row_alt'>";
			}
			else {
				echo "<tr class='row_error'>";
			}
		}
		else if ($t['sched_status'] == 'recording') {
			$ok = 1;
			echo "<tr class='row_inprogress'>";
		}
		else {
			$ok = 0;
			echo "<tr class='row_error'>";
		}
		$summ = $t['disp_extratext'];
		$title = htmlspecialchars($t['disp_title'],ENT_QUOTES);
		$length = sprintf("%d:%02d", $hh, $mm);
		echo "
	  <td class='col_date'>$date
		<span class='thinonly'><br />Time: $time<br />Length: $length</span></td>
	  <td class='wideonly col_time'>$time</td>
	  <td class='col_channel'>
	    <div class='channel_name'>";
		if (isset($settings['ICONS']) && isset($t['channel_icon'])) {
		  print "<img src=\"icon.php?image=$urlp/{$t['channel_icon']}&auth=$auth\" height='48' width='80' alt=\"{$t['channelname']}\" title=\"{$t['channelname']}\">";
		}
		else print "{$t['channelname']}";
		echo "
	    </div>
	  </td>
	  <td class='wideonly col_length'>$length</td>
	  <td class='col_name'><div class='epg_title'>{$title}</div><div class='epg_subtitle'>{$summ}</div></td>
	  <td class='col_delete'><a href='recordings.php?uuid={$t['uuid']}&{$query}'><img src='images/delete.png' title='Delete Recording'></a></td>";
		if ($ok) echo "
	  <td class='col_stream'><a href='$view_url/play/dvrfile/{$t['uuid']}?title={$title}' download='{$t['uuid']}.m3u'><img src='images/play.png' title='Play'></a></td>";
		else echo "<td></td>";
		echo "</tr>";
	}
?>
       </table>
      </div>
     </div>
    </div>
   </div>
  </body>
</html>
