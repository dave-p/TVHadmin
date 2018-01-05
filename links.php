<?php
  $page_title = 'Series Links';
  include_once './head.php';
?>
  <div id="series_list">
    <div id="layout">
      <table id="heading">
        <tr>
	  <td class="col_title"><h1>Series Links</h1></td>
	</tr>
      </table>
      <table class="list">
	<tr class="heading">
	  <td class="col_start"><h2>Timers</h2></td>
	  <td class="col_channel"><h2>Channel</h2></td>
	  <td class="col_channel"><h2>Link</h2></td>
	  <td class="col_name"><h2>Name</h2></td> 
	  <td class="col_delete"></td>
	</tr>
<?php
        $links = get_links();
	$channels = get_channels();
	$timers = get_timers();
	$i = 0;
	foreach($links as $l) {
	    if ($i % 2) {
		echo "<tr class='row_odd'>";
	    }
	    else {
		echo "<tr class='row_even'>";
	    }
	    $lc = $l["channel"];
	    foreach($channels as $c) {
		if ($lc === $c["uuid"]) {
		    $channelname = $c["name"];
		    break;
		}
	    }
	    $n = 0;
	    foreach($timers as $t) {
		if ($t["autorec"] === $l["uuid"]) $n++;
	    }
	    $l['title'] = stripslashes($l['title']);
	    $crid = strrchr($l['serieslink'], '/');
	    echo "
	<td class='col_start'>$n</td>
	<td class='col_channel'>$channelname</td>
	<td class='col_channel'>$crid</td>
	<td class='col_name'>{$l['title']}</td>
	<td class='col_delete'><a href='delete-series.php?uuid={$l['uuid']}'><img src='images\delete.png'></a></td>
      </tr>\n";
	    $i++;
	}
 ?>
     </table>
    </div>
   </div>
  </div>
  </body>
</html>
