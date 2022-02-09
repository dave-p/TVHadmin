<?php
  $page_title = 'System Status';
  include_once './head.php';

  echo "
<script>
window.addEventListener('load',function(event) {
    var tableArr = document.getElementsByClassName('list');
    var cellWidth = 0;

    // get widest
    for(i = 0; i < tableArr.length; i++)
    {
           if(cellWidth < tableArr[i].rows[1].cells[0].clientWidth)
                cellWidth = tableArr[i].rows[1].cells[0].clientWidth;
    }

    // set all columns to the widest width found
    for(i = 0; i < tableArr.length; i++)
    {
            tableArr[i].rows[1].cells[0].style.width = cellWidth+'px';
    }
},false);
</script>
  ";

  $params = array(
	'subs'	=> 'Subscribers',
	'weight'=> 'Weight',
	'bps'	=> 'Bandwidth',
	'ber'	=> 'Bit Error Rate',
	'unc'	=> 'Uncorrected Blocks',
	'te'	=> 'Transport Errors',
	'cc'	=> 'Continuity Errors',
	'signal'=> 'Signal Strength',
	'snr'	=> 'Signal / Noise Ratio',
	'stream'=> 'Stream',
	'ec_block'  => 'Block Error Count',
	'tc_bit'    => 'Total Bit Error Count',
	'tc_block'  => 'Total Block Error Count',
	'ec_bit'    => 'Bit Error Count');

  if (isset($_POST['uuid'])) clear_input_stats($_POST['uuid']);

  function get_input_status() {
	global $urlp;
	$url = "$urlp/api/status/inputs";
	$json = file_get_contents($url);
	$j = json_decode($json, true);
	$ret = &$j["entries"];
	return $ret;
  }

  function get_server_info() {
	global $urlp;
	$url = "$urlp/api/serverinfo";
	$json = file_get_contents($url);
	$j = json_decode($json, true);
	return $j;
  }

  function clear_input_stats($uuid) {
    global $urlp;
    $url = "$urlp/api/status/inputclrstats?uuid=$uuid";
    file_get_contents($url);
  }

  $info = get_server_info();
  $currentload = simplexml_load_file("$urlp/status.xml");
  echo "
<div id='layout'>
  <div id='banner'>
    <table>
      <tr>
        <td class='col_title'><div id='mobmenu'>&#9776;</div> <h1>System Status</h1></td>
      </tr>
    </table>
  </div>
  <div id='wrapper'>
    <div id='content'>
      <table class='list'>
        <tr class='heading'>
          <td class='col_name' colspan=2><h2>System Information</h2></td>
        </tr>
        <tr class='row_alt'>
          <td class='col_channel'>Software Version</td>
          <td class='col_name'>{$info['sw_version']}</td>
        </tr>";
  if ($currentload !== FALSE) {
    $load = preg_replace('/(\d{2})\d*/', '$1', $currentload->systemload);
    $load = preg_replace('/,/', ', ', $load);
    if (isset($currentload->recordings->recording->next)) {
      $recstatus = 'Next: ' . date('Y-m-d H:i', time()+60*$currentload->recordings->recording->next);
    }
    else if (isset($currentload->recordings->recording->title)) {
      $recstatus = '"' . $currentload->recordings->recording->title . '" Ends ' .
		$currentload->recordings->recording->stop->time;
    }
    else $recstatus = "";
    echo "
        <tr class='row_alt'>
          <td class='col_channel'>System Load</td>
          <td class='col_name'>$load</td>
        </tr>
        <tr class='row_alt'>
          <td class='col_channel'>Recording</td>
          <td class='col_name'>$recstatus</td>
        </tr>
        <tr class='row_alt'>
          <td class='col_channel'>Subscriptions</td>
          <td class='col_name'>{$currentload->subscriptions}</td>
        </tr>";
  }
  echo "
      </table>
      <table class='list'>
        <tr class='heading'>
          <td class='col_name'><h2>Tuners</h2></td>";
  $stats = get_input_status();
  foreach($stats as &$s) {
    echo "
          <td class='col_name'>
            <h2>{$s['input']}</h2>
          </td>";
    switch($s['signal_scale']) {
      case 1:
        $s['signal'] = round($s['signal'] * 100 / 65535) . ' %';
        break;
      case 2:
        $s['signal'] = round($s['signal'] / 1000, 1) . ' dBm';
        break;
      default:
        $s['signal'] = 0;
    }
    switch($s['snr_scale']) {
      case 1:
        $s['snr'] = round($s['snr'] * 100 / 65535) . ' %';
        break;
      case 2:
        $s['snr'] = round($s['snr'] / 1000, 1) . ' dB';
        break;
      default:
        $s['snr'] = 0;
    }
  }
  unset($s);
  echo "
        </tr>
        <tr>
          <td></td>";
  foreach($stats as $s) {
  echo "
          <td class='col_name'>
            <form name='clear' method='POST' action='status.php'>
              <input type='submit' name='clearcounts' value='Clear Counters'>
              <input type='hidden' name='uuid' value='{$s["uuid"]}'>
            </form>
          </td>";
	}
  echo "
        </tr>";
  foreach($params as $k => $v) {
    echo "
        <tr class='row_alt'>
          <td class='col_channel'>$v</td>";
    foreach($stats as $s) {
      if(isset($s[$k])) echo "<td class='col_name'>{$s[$k]}</td>";
      else echo "<td class='col_name'></td>";
    }
    echo "
        </tr>";
  }
?>
      </table>
     </div>
    </div>
   </div>
  </div>
 </body>
</html>
