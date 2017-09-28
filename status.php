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
	'snr_scale' => 'SNR Scale',
	'ec_block'  => 'Block Error Count',
	'tc_bit'    => 'Total Bit Error Count',
	'signal_scale' => 'Signal Scale',
	'tc_block'  => 'Total Block Error Count',
	'ec_bit'    => 'Bit Error Count');

  function get_input_status() {
	global $urlp;
	$url = "$urlp/api/status/inputs";
	$json = file_get_contents($url);
	$j = json_decode($json, true);
	$ret = &$j["entries"];
	return $ret;
  }
?>
  <div id="status">
    <div id="layout">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="heading">
        <tr>
	  <td class="col_title"><h1>System Status</h1></td>
	</tr>
      </table>
<?php
	$i = 0;
	$stats = get_input_status();
	foreach($stats as $s) {
	    echo "
      <table width='100%' border=0 cellpadding=0 class='list hilight'>
        <tr class='heading'>
          <td class='col_name' colspan=2><h2>{$s['input']}</h2></td> 
        </tr>";
	    foreach($s as $key => $val) {
		if ($key == 'input') continue;
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
