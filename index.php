<?php
/*
========================================
Code by Steven Andrew Smith (IGSteven)
         Streamers Connected

   https://linkedin.com/in/igsteven
========================================
*/
$livestreams = json_decode(file_get_contents("https://ashley.streamersconnected.tv/data/streamlist.json"), true);
$livestreams = array_slice($livestreams, 0, 32);
?>
<h1>Live Streams</h1><hr>
<?php
foreach ($livestreams as $key => $livestream) {	
  if($livestream['type'] == "twitch"){
    $baseurl = "https://twitch.tv/";
  }
  elseif($livestream['type'] == "mixer"){
      $baseurl = "https://mixer.com/";
  }
  ?>
  <?php echo $livestream['name'] ?>
  <img src="<?php echo $livestream['thumbnail_url'] ?>">
  <?php echo $baseurl.$livestream['name']
  echo number_format($livestream['viewer_count']);
}
?>
    
