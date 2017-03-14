<?php
  $page_title = 'Configuration';
  include_once('head.php');
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
 <div id="config">
    <form action="configure.php" method="post" name="FormName">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="heading">
	    <tr>
		<td class="col_title">
			<h1>Configuration</h1>
		</td>
	    </tr>
	</table>

	<table border="0" cellspacing="0" cellpadding="0" class="group">
	    <tr class="heading">
		<td colspan="2"><h2>TVHeadend Server</h2></td>
	    </tr>
	    <tr class="row_odd">
		<td class="col_label"><h5>TVH Username:</h5></td>
<?php
		include_once "include.php";
		echo "<td class=\"col_value\"><input type=\"text\" name=\"USER\" value=\"$user\" size=\"16\" /></td>";
?>
	    </tr>
	    <tr class="row_even">
		<td class="col_label"><h5>TVH Password:</h5></td>
<?php
		echo "<td class=\"col_value\"><input type=\"password\" name=\"PASS\" value=\"$pass\" size=\"16\" /></td>";
?>
	    </tr>
	    <tr class="row_odd">
		<td class="col_label"><h5>TVH IP Address:</h5></td>
<?php
		echo "<td class=\"col_value\"><input type=\"text\" name=\"IP\" value=\"$ip\" size=\"24\" /></td>";
?>
	    </tr>
	</table>

	<table border="0" cellspacing="0" cellpadding="0" class="group">
	    <tr class="heading">
		<td colspan="2"><h2>Preferences</h2></td>
	    </tr>
	    <tr class="row_odd">
		<td class="col_label"><h5>EPG Day starts at:</h5></td>
		<td class="col_value">
		    <select name="EPGSTART">
<?php
	for ($st=0; $st<7; $st++) {
	  echo "<option value=\"$st\"";
	  if ($st == $epg_start) echo " selected";
	  echo ">$st:00</option>";
	}
?>
		    </select>
		</td>
	    </tr>
	</table>

	<table border="0" cellspacing="0" cellpadding="0" class="group">
	    <tr class="heading">
		<td colspan="3"><h2>Channel Selections</h2></td>
	    </tr>
	    <tr class="row_odd">
		<td class="col_channels">
		    <select name="all_channels" size="8" multiple="multiple" class="channels">
<?php
        $chans = get_channels();
        foreach($chans as $v) {
            $cname = $v["val"];
            print "<option value=\"$cname\">$cname</option>";
        }
?>
		    </select>
		</td>
		<td>
		    <input type="button" onClick="one2two()" value="&gt;&gt;&gt;&gt;&gt;" /><br />
		    <input type="button" onClick="two2one()" value="&lt;&lt;&lt;&lt;&lt;" />
		</td>
		<td class="col_wanted_channels">
		    <select name="selected_channels[]" size="8" multiple="multiple" class="channels">
<?php
	$sel = &$settings['selected_channels'];
	foreach($sel as $s) {
	    echo "<option value=\"$s\">$s</option>";
	}
?>
		    </select>
		</td>
	    </tr>
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
</body>
</html>
