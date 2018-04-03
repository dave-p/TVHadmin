<?php
  $page_title = 'Recordings';
  include_once './head.php';
  if (isset($_GET["uuid"])) {
    $uuid = $_GET["uuid"];
    $url = "$urlp/api/dvr/entry/remove?uuid=$uuid";
    file_get_contents($url);
  }
  if (!isset($sort)) $sort = 0;
  if (isset($_POST['last_sort'])) $sort = 1 - $_POST['last_sort'];
  $chtype = array();
  $media = array();
  $sv = get_services();
  foreach ($sv as $s) {
      foreach ($s["channel"] as $c) {
          $chtype[$c] = $s["dvb_servicetype"];
      }
  }
  if (array_key_exists('NOANON', $settings)) {
      $view_url = $urlp;
  }
  else {
      $view_url = 'http://' . $ip;
  }
  echo "
    <script type='text/javascript'>
      function formSubmit() {
        document.media.submit();
      }
    </script>
    <div id='layout'>
      <div id='banner'>
	<table>
	  <tr>
	      <td class='col_title'><div id='mobmenu'>&#9776;</div> <h1>Recordings</h1>
	      </td>
	      <td>
		<form name='order' method='POST' action='recordings.php'>
		  <input type='submit' name='sort' value='Sort by {$orders[1-$sort]}'>
		  <input type='hidden' name='last_sort' value='$sort'>
		</form>
	      </td>
	      <td><span class='wideonly'>
		<form name='media' method='GET' action='recordings.php'>
		  <input type='hidden' name='update' value='1'>
	      ";
  foreach (array_flip($types) as $t=>$v) {
      echo "
	<div class='media'>
	  <label for='$t'>$t:</label>
	  <input type='checkbox' name='$t' id='$t' onchange='formSubmit()'";
      if (isset($_GET['update'])) {
          if (isset($_GET[$t])) {
              $media[$t] = 1;
              echo " checked";
           }
      }
      else {
          $g = "Rec_" . $t;
          if (isset($settings[$g])) {
              $media[$t] = 1;
              echo " checked";
          }
      }
      echo ">
	</div>";
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
	      <td class='col_time'><h2>Time</h2></td>
	      <td class='col_channel'><h2>Channel</h2></td>
	      <td class='col_length'><h2>Length</h2></td>
	      <td class='col_name'><h2>Name</h2></td>
	      <td class='col_delete'></td>
	      <td class='col_stream'></td>
	    </tr>
  ";
        $recordings = get_recordings($sort);
	$i = 0;
	foreach($recordings as $t) {
		if ($t["sched_status"] == "scheduled") continue;
		$cid = $t["channel"];
		$typeno = $chtype[$cid];
		$ctname = $types[$typeno];
		if (!array_key_exists($ctname, $media)) goto nogood;
		$time = strftime("%H:%M", $t["start"]);
		$date = strftime("%a %e/%m/%y", $t["start"]);
		$duration = $t["stop_real"] - $t["start_real"];
		$hh = $duration / 3600;
		$mm = ($duration % 3600) / 60;
		if ($t['sched_status'] == 'completed') {
			$ok = 1;
			if ($i % 2) {
				echo "<tr class='row_odd'>";
			}
			else {
				echo "<tr class='row_even'>";
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
		if ($settings['SUMM'] == 'summary') {
			$summ = 'disp_description';
		}
		else {
			$summ = 'disp_subtitle';
		}
		$title = htmlspecialchars($t['disp_title'],ENT_QUOTES);
		echo "
	  <td class='col_date'>$date</td>
	  <td class='col_time'>$time</td>
	  <td class='col_channel'>{$t['channelname']}</td>";
		printf("<td class='col_length'>%d:%02d</td>", $hh, $mm);
		echo "
	  <td class='col_name'><div class='epg_title'>{$title}</div><div class='epg_subtitle'>{$t[$summ]}</div></td>
	  <td class='col_delete'><a href='recordings.php?uuid={$t['uuid']}'><img src='images\delete.png' title='Delete Recording'></a></td>";
		if ($ok) echo "
	  <td class='col_stream'><a href='$view_url/play/dvrfile/{$t['uuid']}?title={$title}'><img src='images\play.png' title='Play'></a></td>";
		else echo "<td></td>";
		echo "</tr>";
		$i++;
nogood:
	}
?>
       </table>
      </div>
     </div>
    </div>
   </div>
  </body>
</html>
