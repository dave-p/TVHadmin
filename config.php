<?php
  $page_title = 'Configuration';
  include_once('./head.php');
  $clashmodes = array(
      0 => "Off:",
      1 => "Single tuner:",
      2 => "Multiple tuners:");
  $tags = get_channeltags();
?>
    <script>
	function one2two() {
	    m1len = m1.length ;
	    for ( i=0; i<m1len ; i++){
		if (m1.options[i].selected == true ) {
		    m2len = m2.length;
		    m2.options[m2len]= new Option(m1.options[i].text);
		}
	    }

	    for ( i = (m1len -1); i>=0; i--){
		if (m1.options[i].selected == true ) {
		    m1.options[i] = null;
		}
	    }
	}

	function two2one() {
	    m2len = m2.length ;
	    for ( i=0; i<m2len ; i++){
		if (m2.options[i].selected == true ) {
		    m1len = m1.length;
		    m1.options[m1len]= new Option(m2.options[i].text);
		}
	    }
	    for ( i=(m2len-1); i>=0; i--) {
		if (m2.options[i].selected == true ) {
		    m2.options[i] = null;
		}
	    }
	}

	function selectAll() {
	    for (i=0; i<m2.length; i++) {  
		m2.options[i].selected = true; 
	    }
	}
    </script>

<div id="layout">
  <div id="banner">
	<table>
	    <tr>
		<td class="col_title">
			<div id='mobmenu'>&#9776;</div> <h1>Configuration</h1>
		</td>
	    </tr>
	</table>
  </div>
  <div id='wrapper'>
    <div id='config'>
      <form action="configure.php" method="post" name="FormName">
	<table class="group">
	    <tr class="heading">
		<td colspan="2"><h2>TVHeadend Server</h2></td>
	    </tr>
	    <tr class="row_alt" title="Name of a TVheadend user with admin privilege">
		<td class="col_label"><h5>Username:</h5></td>
<?php
		echo "<td class='col_value'><input type='text' name='USER' value='$user' size='16'>";
?>
		</td>
	    </tr>
	    <tr class="row_alt" title="Password of the TVheadend user">
		<td class="col_label"><h5>Password:</h5></td>
<?php
		echo "<td class='col_value'><input type='password' name='PASS' value='$pass' size='16'></td>";
?>
	    </tr>
	    <tr class="row_alt" title="IP address & port of the TVheadend server, eg 192.168.0.1:9981">
		<td class="col_label"><h5>IP Address:port:</h5></td>
<?php
		echo "<td class='col_value'><input type='text' name='IP' value='$ip' size='24'></td>";
?>
	    </tr>
	    <tr class='row_alt' title="TVheadend profile to use for recordings.">
<?php
	$prof = get_profiles();
	if (isset($prof)) {
	  echo "<td class='col_label'><h5>Recording Profile:</h5></td>";
	  echo "<td class='col_value'><select name='UUID'>";
	  if (!isset($config_uuid)) $config_uuid = $prof[0]['uuid'];
	  foreach ($prof as $p) {
	    if (!$p['enabled']) continue;
	    $pname = $p['name'];
	    if ($pname == '') $pname = '(default)';
	    if ($p['uuid'] === $config_uuid) $sel = 'selected';
	    else $sel = '';
	    echo "<option value='{$p['uuid']}' $sel>$pname</option>";
	  }
?>
		</td>
	    </tr>
	    <tr class='row_alt' title="TVheadend profile to use for streaming.">
	      <td class='col_label'><h5>Streaming Profile:</h5></td>
	      <td class='col_value'><select name='SUUID'>
<?php
	  $sprof = get_stream_profiles();
	  foreach ($sprof as $p) {
	    $sel = '';
	    if (isset($config_suuid)) {
	      if ($p['uuid'] === $config_suuid) $sel = 'selected';
	    }
	    else {
	      if ($p['default']) $sel = 'selected';
	    }
	    echo "<option value='{$p['uuid']}' $sel>{$p['name']}</option>";
	  }
?>
	    </tr>
	</table>
	<table class='group'>
	    <tr class='heading'>
		<td colspan='2'><h2>Preferences</h2></td>
	    </tr>
	    <tr class='row_alt' title='Select display theme'>
		<td class='col_label'><h5>Display theme:</h5></td>
		<td class='col_value'>
<?php
	if (!isset($settings['THEME'])) $settings['THEME'] = 0;
	foreach ($themes as $key=>$value) {
	  echo "<label for='C$key'>$value:</label>";
	  echo "<input type='radio' name='THEME' id='C$key' value='$key'";
	  if ($key == $settings['THEME']) echo " checked>";
	  else echo ">";
	}
                  echo "
                </td>
            </tr>
	    <tr class='row_alt' title='Time after which entries appear on tomorrow&apos;s EPG. Useful if you watch TV after midnight'>
		<td class='col_label'><h5>EPG Day starts at:</h5></td>
		<td class='col_value'>
		    <select name='EPGSTART'>";
	for ($st=0; $st<7; $st++) {
	  echo "<option value='$st'";
	  if (isset($epg_start) && ($st == $epg_start)) echo " selected";
	  echo ">$st:00</option>";
	}
		  echo "</select>
		</td>
	    </tr>
	    <tr class='row_alt' title='Screen shown when entering TVHadmin'>
		<td class='col_label'><h5>Home Page:</h5></td>
		<td class='col_value'>
		    <select name='HOME'>";
	foreach ($pages as $key=>$value) {
	  echo "<option value='$key'";
	  if (isset($settings['HOME']) && ($key == $settings['HOME'])) echo " selected";
	  echo ">$value</option>";
	}
		echo "</select>
		</td>
	    </tr>
	    <tr class='row_alt' title='Sort channels by name or Logical Channel Number'>
		<td class='col_label'><h5>Channels Sort Order:</h5></td>
		<td class='col_value'>";
	if (!isset($settings['CSORT'])) $settings['CSORT'] = 0;
	foreach ($c_orders as $key=>$value) {
	  echo "<label for='C$key'>$value:</label>";
	  echo "<input type='radio' name='CSORT' id='C$key' value='$key'";
	  if ($key == $settings['CSORT']) echo " checked>";
	  else echo ">";
	}
                  echo "
                </td>
            </tr>
	    <tr class='row_alt' title='Which TVH tags are to be used for filtering. Click &apos;Save&apos; after changing this selection to update the other preferences'>
		<td class='col_label'><h5>Media Tags to use for selection:</h5></td>
		<td class='col_value'>";
	foreach ($tags as $t=>$v) {
	  $g = urlencode("Tag_" . $t);
	  echo "$t: <input type='checkbox' name='$g' ";
	  if (isset($settings[$g])) {
	    echo " checked";
	  }
	  echo ">";
	}
	echo "
		</td>
	    </tr>
	    <tr class='row_alt' title='Refresh the Whats On Now and Timeline screens every minute'>
		<td class='col_label'><h5>Refresh Whats On Now and Timeline screens:</h5></td>
		<td class='col_value'><input type='checkbox' name='REFR'";
	if (isset($settings['REFR'])) {
	  echo " checked";
	}
	echo ">
		</td>
	    </tr>
	    <tr class='row_alt' title='Log in to TVheadend when viewing recordings or live TV through TVHadmin. Not needed if you have an anonymous (&apos;*&apos;) user'>
		<td class='col_label'><h5>Send user/pass when Viewing:</h5></td>
		<td class='col_value'><input type='checkbox' name='NOANON'";
        if (isset($settings['NOANON'])) {
          echo " checked";
        }
        echo ">
		</td>
	    </tr>
           <tr class='row_alt' title='Show icon instead of channel name. Needs picons installed and imagecache enabled on TVHeadend'>
                <td class='col_label'><h5>Show channel icons in What's On Now & Recordings:</h5></td>
                <td class='col_value'><input type='checkbox' name='ICONS'";
        if (isset($settings['ICONS'])) {
          echo " checked";
        }
        echo ">
                </td>
            </tr>
	    <tr class='row_alt' title='Show timer conflicts. Multiple tuner option is experimental and needs ADMIN privilege.'>
		<td class='col_label'><h5>Detect timer clashes:</h5></td>
		<td class='col_value'>";
	if (!isset($settings['CLASHMODE'])) $settings['CLASHMODE'] = 0;
	foreach ($clashmodes as $key=>$value) {
	  echo "<label for='T$key'>$value</label>";
	  echo "<input type='radio' name='CLASHMODE' id='T$key' value='$key'";
	  if ($key == $settings['CLASHMODE']) echo " checked>";
	  else echo ">";
	}
	echo "</td>
	    </tr>
	    <tr class='row_alt' title='Time period for the Timeline screen'>
		<td class='col_label'><h5>Timeline length:</h5></td>
		<td class='col_value'>
		    <select name='TIMESPAN'>";
	foreach (array(2, 4, 6) as $st) {
	  echo "<option value='$st'";
	  if (isset($settings['TIMESPAN']) && ($st == $settings['TIMESPAN'])) echo " selected";
	  echo ">$st hours</option>";
	}
		  echo "</select>
		</td>
	    </tr>
	</table>

	<table class='group'>
	    <tr class='heading'>
		<td colspan='2'><h2>Defaults</h2></td>
	    </tr>
	    <tr class='row_alt' title='Default filter setting for the What&apos;s On Now 
screen'>
		<td class='col_label'><h5>Show in What's On Now:</h5></td>
		<td class='col_value'>";
	foreach ($tags as $t=>$v) {
	  $ut = urlencode($t);
	  if (isset($settings["Tag_$ut"])) {
	    $g = "Media_" . $ut;
	    echo "$t: <input type='checkbox' name='$g' ";
	    if (isset($settings[$g])) {
		echo " checked";
	    }
	    echo ">";
	  }
	}
	echo "
		</td>
	    </tr>
	    <tr class='row_alt' title='Default filter setting for the Timeline screen'>
		<td class='col_label'><h5>Show in Timeline:</h5></td>
		<td class='col_value'>";
	foreach ($tags as $t=>$v) {
	  $ut = urlencode($t);
	  if (isset($settings["Tag_$ut"])) {
	    $g = "Time_" . $ut;
	    echo "$t: <input type='checkbox' name='$g' ";
	    if (isset($settings[$g])) {
	      echo " checked";
	    }
	    echo ">";
	  }
	}
	echo "
		</td>
	    </tr>
	    <tr class='row_alt' title='Default filter setting for Recordings'>
		<td class='col_label'><h5>Show in Recordings:</h5></td>
		<td class='col_value'>";
	foreach ($tags as $t=>$v) {
	  $ut = urlencode($t);
	  if (isset($settings["Tag_$ut"])) {
	    $g = "Rec_" . $ut;
	    echo "$t: <input type='checkbox' name='$g' ";
	    if (isset($settings[$g])) {
	      echo " checked";
	    }
	    echo ">";
	  }
	}
	echo "
		</td>
	    </tr>
	    <tr class='row_alt' title='Sort recordings by date or by title (ignoring &apos;New:&apos;)'>
		<td class='col_label'><h5>Recordings Sort Order:</h5></td>
		<td class='col_value'>";
	if (!isset($settings['SORT'])) $settings['SORT'] = 0;
	foreach ($orders as $key=>$value) {
	  echo "<label for='B$key'>$value:</label>";
	  echo "<input type='radio' name='SORT' id='B$key' value='$key'";
	  if ($key == $settings['SORT']) echo " checked>";
	  else echo ">";
	}
		  echo "
		</td>
	    </tr>
	</table>

	<table class='group'>
	    <tr class='heading'>
		<td colspan='3'><h2>Favourite Channels</h2></td>
	    </tr>
	    <tr class='row_alt'>
		<td class='col_channels'>
		    <select name='all_channels' size='8' multiple='multiple' class='channels'>";
	    $chans = get_channels();
	    foreach($chans as $v) {
		$cname = $v["name"];
		print "<option value='$cname'>$cname</option>";
	    }
		    echo "</select>
		</td>
		<td style='text-align: center;'>
		    <input type='button' onClick='one2two()' value='&gt;&gt;&gt;&gt;&gt;'><br>
		    <input type='button' onClick='two2one()' value='&lt;&lt;&lt;&lt;&lt;'>
		</td>
		<td class='col_wanted_channels'>
		    <select name='selected_channels[]' size='8' multiple='multiple' class='channels'>";
	    $sel = &$settings['selected_channels'];
	    foreach($sel as $s) {
		echo "<option value='$s'>$s</option>";
	    }
		    echo "</select>
		</td>
	    </tr>";
	    $save = "Save";
	}
	else {
	    echo "
	    <input type='hidden' name='Tag_All' value='on'>
	    <input type='hidden' name='Media_All' value='on'>
	    <input type='hidden' name='Time_All' value='on'>
	    <input type='hidden' name='Rec_All' value='on'>";
	    $save = "Connect";
	}
	echo "
	</table>
	<div id='buttons'>
	    <input type='submit' class='submit' name='save' value=$save onclick='selectAll()'>
	</div>
    </form>";
?>
    <script>
// IMPORTANT: this is the extra bit of code
// shorthand for referring to menus
// must run after document has been created
// you can also change the name of the select menus and
// you would only need to change them in one spot, here
        var m1 = document.FormName.all_channels;
        var m2 = document.FormName.elements['selected_channels[]'];
    </script>
   </div>
  </div>
 </div>
</div>
</body>
</html>
