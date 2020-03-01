<?php
require_once('define.php');
$ch = curl_init('https://api.line.me/v2/bot/richmenu/list');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charser=UTF-8',
    'Authorization: Bearer '.$LINEINFO['access_token']
));
$result = json_decode(curl_exec($ch));
curl_close($ch);

foreach($result->{"richmenus"} as $value){
    $ch = curl_init('https://api.line.me/v2/bot/richmenu/'.$value->{"richMenuId"});
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer '.$LINEINFO['access_token']
    ));
    $result = json_decode(curl_exec($ch));
    curl_close($ch);
    echo "yash\n";
}