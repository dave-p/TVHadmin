<!DOCTYPE html> 
<html>
<head>
<meta charset=utf-8>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="application-name" content="TVHadmin">
<title>TVHadmin - <?php echo $page_title; ?></title>
<link rel="stylesheet" href="style.css">
<!--[if !IE]> -->
<script>
  window.addEventListener('load',function() {
    var mytop=document.getElementById("mobmenu");
    mytop.addEventListener('click', function() {
        var mynav=document.getElementById("navigation");
        if (mynav.classList.contains('focus'))
            mynav.classList.remove('focus');
        else mynav.classList.add('focus');
    });
  },false);
</script>
<!-- <![endif]-->
</head> 
<body>
<div id="container">
  <div id="navigation">
    <div class="logo">
      <img src="images/logo.png" alt="TVHeadend Logo" width="150" border="0">
    </div>
    <div class="nav_bar">
<?php
  include_once './include.php';
  foreach ($pages as $key=>$value) {
    echo "<div class='navi'><a href='TVHadmin.php?screen=$key'>$value</a></div>";
  }
?>
      <form action="search.php" method="GET" name="telly" class="search">
        <input type="text" name="find"><br>
        <input type="submit" name="submit" value="Search">
      </form>
    </div>
  </div>
