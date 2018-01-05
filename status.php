<?php
  $page_title = 'System Status';
  include_once './head.php';

  $params = array(
	'uuid'	=> 'uuid',
	'subs'	=> 'Subscribers',
	'weight'=> 'Weight',
	'bps'	=> 'Bandwidth',
	'ber'	=> 'BER',
	'unc'	=> 'Uncorrected Blocks',
	'te'	=> 'Transport Errors',
	'cc'	=> 'Continuity Errors',
	'signal'=> 'Signal Strength',
	'snr'	=> 'SNR',
	'stream'=> 'Stream',
	'ec_block'  => 'Block Error Count',
	'tc_bit'    => 'Total Bit Error Count',
	'tc_block'  => 'Total Block Error Count',
	'ec_bit'    => 'Bit Error Count');

  $ignore = array(
	'input'	=> 1,
	'snr_scale' => 'SNR Scale',
	'signal_scale' => 'Signal Scale');

  if (isset($_POST['uuid'])) clear_input_stats($_POST['uuid']);

  function get_input_status() {
	global $urlp;
	$url = "$urlp/api/status/inputs";
	$json = file_get_contents($url);
	$j = json_decode($json, true);
	$ret = &$j["entries"];
	return $ret;
  }

  function clear_input_stats($uuid) {
    global $urlp;
    $url = "$urlp/api/status/inputclrstats?uuid=$uuid";
    file_get_contents($url);
  }
?>
  <div id="status">
    <div id="layout">
	<table id="heading">
	  <tr>
	    <td class="col_title"><h1>System Status</h1></td>
	  </tr>
	</table>
<?php
	$stats = get_input_status();
	foreach($stats as $s) {
	  $i = 0;
	  echo "
      <table class='list'>
	<tr class='heading'>
	  <td class='col_name' colspan=2> 
	    <form name='clear' method='POST' action='status.php'><h2>{$s['input']}</h2>
	    <input type='submit' name='clearcounts' value='Clear Counters'>
	    <input type='hidden' name='uuid' value='{$s["uuid"]}'></form></td>
	</tr>";
	    switch($s['signal_scale']) {
		case 1:
		  $s['signal'] = $s['signal'] * 100 / 65535 . ' %';
		  break;
		case 2:
		  $s['signal'] = round($s['signal'] / 1000, 1) . ' dBm';
	    }
	    switch($s['snr_scale']) {
		case 1:
		  $s['snr'] = $s['snr'] * 100 / 65535 . ' %';
                  break;
		case 2:
		  $s['snr'] = round($s['snr'] / 1000, 1) . ' dB';
	    }    
	    foreach($s as $key => $val) {
		if (isset($ignore[$key])) continue;
		if ($i % 2) {
		    echo "<tr class='row_odd'>";
		}
		else {
		    echo "<tr class='row_even'>";
		}
	    echo "
	<td class='col_channel'>{$params[$key]}</td>
	<td class='col_name'>$val</td>
      </tr>\n";
	    $i++;
	    }
	    echo "</table>";
	}
 ?>
     </table>
    </div>
   </div>
  </div>
  </body>
</html>
