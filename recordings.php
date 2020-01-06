<?php
  $page_title = 'Recordings';
  include_once './head.php';
  if (isset($_GET["uuid"])) {
    $uuid = $_GET["uuid"];
    $url = "$urlp/api/dvr/entry/remove?uuid=$uuid";
    file_get_contents($url);
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
  $tag = array('All' => 'All');
  foreach ($tags as $t) {
      $tag[$t["key"]] = $t["val"];
  }
  echo "
    <script type='text/javascript'>
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
		<form name='order' method='GET' action='recordings.php'>";
  foreach ($orders as $key=>$value) {
    echo "<div class='media'><label for='B$key'>$value:</label>";
    echo "<input type='radio' name='SORT' id='B$key' value='$key' onChange='formSubmit(\"order\")'";
    if ($key == $sort) echo " checked></div>";
    else echo "></div>";
  }
  echo "
		</form>
	      </td>
	      <td><span class='wideonly'>
		<form name='media' method='GET' action='recordings.php'>
		  <input type='hidden' name='update' value='1'>
		  <input type='hidden' name='SORT' value='$sort'>
	      ";
  foreach ($tag as $v=>$t) {
    $tt = urlencode($t);
    if (isset($settings["Tag_$tt"])) {
      echo "
	<div class='media'>
	  <label for='$tt'>$t:</label>
	  <input type='checkbox' name='$tt' id='$tt' onchange='formSubmit(\"media\")'";
      if (isset($_GET['update'])) {
          if (isset($_GET[$tt])) {
              $media[$t] = 1;
              echo " checked";
           }
      }
      else {
          $g = "Rec_" . $tt;
          if (isset($settings[$g])) {
              $media[$t] = 1;
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
	$i = 0;
	foreach($recordings as $t) {
		if ($t["sched_status"] == "scheduled") continue;
		if (!isset($media["All"])) {
			$cid = $t["channel"];
			if (array_key_exists($cid, $chtags)) {
				foreach($chtags[$cid] as $c) {
				    if (array_key_exists($tag[$c], $media)) goto good;
				}
				continue;
			}
		}
good:
		$time = strftime("%H:%M", $t["start"]);
		$date = strftime("%a %e/%m/%y", $t["start"]);
		$duration = $t["stop"] - $t["start"];
		$hh = $duration / 3600;
		$mm = ($duration % 3600) / 60;
		if ($t['sched_status'] == 'completed') {
			$ok = 1;
			if ($t['status'] == 'Completed OK') {
				if ($i % 2) {
					echo "<tr class='row_odd'>";
				}
				else {
					echo "<tr class='row_even'>";
				}
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
		if ($settings['SUMM'] == 'summary') {
			$summ = 'disp_description';
		}
		else {
			$summ = 'disp_subtitle';
		}
		$title = htmlspecialchars($t['disp_title'],ENT_QUOTES);
		$length = sprintf("%d:%02d", $hh, $mm);
		echo "
	  <td class='col_date'>$date
		<span class='thinonly'><br />Time: $time<br />Length: $length</span></td>
	  <td class='wideonly col_time'>$time</td>
	  <td class='col_channel'>{$t['channelname']}</td>
	  <td class='wideonly col_length'>$length</td>
	  <td class='col_name'><div class='epg_title'>{$title}</div><div class='epg_subtitle'>{$t[$summ]}</div></td>
	  <td class='col_delete'><a href='recordings.php?uuid={$t['uuid']}&SORT=$sort'><img src='images\delete.png' title='Delete Recording'></a></td>";
		if ($ok) echo "
	  <td class='col_stream'><a href='$urlp/play/dvrfile/{$t['uuid']}?title={$title}'><img src='images\play.png' title='Play'></a></td>";
		else echo "<td></td>";
		echo "</tr>";
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
