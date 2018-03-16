<?php
  $page_title = 'Configuration';
  include_once('./head.php');
?>
    <script language= "JavaScript">

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
	    <tr class="row_odd">
		<td class="col_label"><h5>Username:</h5></td>
<?php
		echo "<td class=\"col_value\"><input type=\"text\" name=\"USER\" value=\"$user\" size=\"16\" /></td>";
?>
	    </tr>
	    <tr class="row_even">
		<td class="col_label"><h5>Password:</h5></td>
<?php
		echo "<td class=\"col_value\"><input type=\"password\" name=\"PASS\" value=\"$pass\" size=\"16\" /></td>";
?>
	    </tr>
	    <tr class="row_odd">
		<td class="col_label"><h5>IP Address:port:</h5></td>
<?php
		echo "<td class=\"col_value\"><input type=\"text\" name=\"IP\" value=\"$ip\" size=\"24\" /></td>";
?>
	    </tr>
<?php
	$prof = get_profiles();
	if (isset($prof)) {
	  if(count($prof) == 1) {
		$config_uuid = $prof[0]['uuid'];
		echo "<input type=\"hidden\" name=\"PROFILE\" value=\"\">";
		echo "<input type=\"hidden\" name=\"UUID\" value=\"$config_uuid\">";
	  }
	  else {
		echo "<tr class=\"row_even\">";
		echo "<td class=\"col_label\"><h5>Recording Profile:</h5></td>";
		echo "<td class=\"col_value\"><select name=\"PROFILE\">";
	    foreach ($prof as $p) {
		$pname = $p['name'];
		if ($pname == '') $pname = '(default)';
		if ($p['uuid'] === $config_uuid) $sel = 'selected';
		else $sel = '';
		echo "<option value=\"$pname\" $sel>$pname</option>";
	    }
		echo "</td>";
	    echo "</tr>";
	  }
	echo "</table>

	<table border='0' cellspacing='0' cellpadding='0' class='group'>
	    <tr class='heading'>
		<td colspan='2'><h2>Preferences</h2></td>
	    </tr>
	    <tr class='row_odd'>
		<td class='col_label'><h5>EPG Day starts at:</h5></td>
		<td class='col_value'>
		    <select name='EPGSTART'>";
	for ($st=0; $st<7; $st++) {
	  echo "<option value=\"$st\"";
	  if (isset($epg_start) && ($st == $epg_start)) echo " selected";
	  echo ">$st:00</option>";
	}
		  echo "</select>
		</td>
	    </tr>
	    <tr class='row_even'>
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
	    <tr class='row_odd'>
		<td class='col_label'><h5>Recordings Sort Order:</h5></td>
		<td class='col_value'>
		    <select name='SORT'>";
	foreach ($orders as $key=>$value) {
	  echo "<option value='$key'";
	  if (isset($settings['SORT']) && ($key == $settings['SORT'])) echo " selected";
	  echo ">$value</option>";
	}
		  echo "</select>
		</td>
	    </tr>
	    <tr class='row_even'>
		<td class='col_label'><h5>Show in What's On Now:</h5></td>
		<td class='col_value'>";
	foreach (array_flip($types) as $t=>$v) {
	  $g = "Media_" . $t;
	  echo "$t: <input type='checkbox' name='$g' ";
	  if (isset($settings[$g])) {
	    echo " checked";
	  }
	  echo ">";
	}
	echo "
		</td>
	    </tr>
	    <tr class='row_odd'>
		<td class='col_label'><h5>Show in Recordings:</h5></td>
		<td class='col_value'>";
	foreach (array_flip($types) as $t=>$v) {
	  $g = "Rec_" . $t;
	  echo "$t: <input type='checkbox' name='$g' ";
	  if (isset($settings[$g])) {
	    echo " checked";
	  }
	  echo ">";
	}
	echo "
		</td> 
	    </tr> 
	";
	if (!isset($settings['SUMM'])) $settings[SUMM] = 'summary';
	if ($settings['SUMM'] == 'summary') {
	  $c1 = 'checked'; $c2 = '';
	}
	else {
	  $c1 = ''; $c2 = 'checked';
	}
	echo "
	    <tr class='row_even'>
		<td class='col_label'><h5>Show in EPG and Recordings:</h5></td>
		<td class='col_value'>
		    <input type='radio' name='SUMM' value='summary' $c1>Summary&nbsp;
		    <input type='radio' name='SUMM' value='subtitle' $c2>Subtitle
		</td>
	    </tr>
	    <tr class='row_odd'>
		<td class='col_label'><h5>Send user/pass when Viewing:</h5></td>
		<td class='col_value'><input type='checkbox' name='NOANON'";
          if (isset($settings['NOANON'])) {
            echo " checked";
          }
          echo ">
		</td>
	    </tr>
	</table>

	<table border='0' cellspacing='0' cellpadding='0' class='group'>
	    <tr class='heading'>
		<td colspan='3'><h2>Favourite Channels</h2></td>
	    </tr>
	    <tr class='row_odd'>
		<td class='col_channels'>
		    <select name='all_channels' size='8' multiple='multiple' class='channels'>";
	    $chans = get_channels();
	    foreach($chans as $v) {
		$cname = $v["name"];
		print "<option value=\"$cname\">$cname</option>";
	    }
		    echo "</select>
		</td>
		<td>
		    <input type='button' onClick='one2two()' value='&gt;&gt;&gt;&gt;&gt;' /><br />
		    <input type='button' onClick='two2one()' value='&lt;&lt;&lt;&lt;&lt;' />
		</td>
		<td class='col_wanted_channels'>
		    <select name='selected_channels[]' size='8' multiple='multiple' class='channels'>";
	$sel = &$settings['selected_channels'];
	foreach($sel as $s) {
	    echo "<option value=\"$s\">$s</option>";
	}
		    echo "</select>
		</td>
	    </tr>";
	}
?>
	</table>
	<div id="buttons">
	    <input type="submit" class="submit" name="save" value="Save" onclick="selectAll()" />
	</div>
    </form>
    <script language= "JavaScript">
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
