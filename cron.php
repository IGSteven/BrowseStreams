<?php

$config['TClient'] = ""; #Twitch Client ID
$config['Tcommnity'] = "831ccb45-e159-4873-a61b-74693bdc5756&" #Twitch Team ID
$config['Mteam'] = "46556"; #Mixer Team ID
$config['JsonFile'] = "Streams.json" json file to dump data on


function file_get_contents_curl($url)
{
    $curlHeader = array("Client-ID: $config['TClientID']", "Accept: application/vnd.twitchtv.v5+json");
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeader);
    
    $data = curl_exec($ch);
    curl_close($ch);
    
    return $data;
}

function id_to_name($id){
    $userdata = json_decode(@file_get_contents_curl('https://api.twitch.tv/kraken/users/' . $id), true);
    $name = $userdata['name'];
    return $name;
}


$teamData = json_decode( @file_get_contents_curl( 'https://api.twitch.tv/helix/streams?community_id='.$config['Tcommnity'].'&first=50' ), true);

$twitch = $teamData['data'];

$mixer = json_decode(file_get_contents("https://mixer.com/api/v1/teams/'.$config['Mteam'].'/users"), true);

$streams = "";

foreach ($mixer as $value) {
    if ($value['channel']['online'] == 'true'){
        $streams['M'.$value['channel']['id'].'']['type'] = "mixer";
        $streams['M'.$value['channel']['id'].'']['id'] = $value['channel']['id'];
        $streams['M'.$value['channel']['id'].'']['user_id'] = $value['id'];
        $streams['M'.$value['channel']['id'].'']['name'] = $value['channel']['token'];
        $streams['M'.$value['channel']['id'].'']['viewer_count'] = $value['channel']['viewersCurrent'];
        $streams['M'.$value['channel']['id'].'']['thumbnail_url'] = "https://mixer.com/api/v1/channels/". $value['channel']['id']."/banner";
    } 
}

foreach ($twitch as $value) {
        $streams['T'.$value['id'].'']['type'] = "twitch";
        $streams['T'.$value['id'].'']['id'] = $value['id'];
        $streams['T'.$value['id'].'']['user_id'] = $value['user_id'];
        $streams['T'.$value['id'].'']['name'] = id_to_name($value['user_id']);
        $streams['T'.$value['id'].'']['viewer_count'] = $value['viewer_count'];
        $streams['T'.$value['id'].'']['thumbnail_url'] = str_replace("{width}","384", str_replace("{height}","216", $value['thumbnail_url']));
}

  function compare_lastname($a, $b)
  {
    return strnatcmp($a['viewer_count'], $b['viewer_count']);
  }

  // sort alphabetically by name
  usort($streams, 'compare_lastname');

$streams = array_reverse($streams);
$streams = array_slice($streams, 0, 50);
$encoded = json_encode($streams);
$dothis = file_put_contents($config['JsonFile'], $encoded);
