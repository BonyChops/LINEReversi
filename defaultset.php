<?php


/* '{
    "size": {
      "width": 2500,
      "height": 1686
    },
    "selected": false,
    "name": "Nice richmenu",
    "chatBarText": "Tap here",
    "areas": [
      {
        "bounds": {
          "x": 0,
          "y": 0,
          "width": 2500,
          "height": 1686
        },
        "action": {
          "type": "postback",
          "data": "action=buy&itemid=123"
        }
      }
   ]
}'*/
$data = [
  "status" => "start"
];

$area = [[
    "bounds" => [
        "x" => 0,
        "y" => 0,
        "width" => 1280,
        "height" => 810
    ],
    "action" => [
        "type" => "postback",
        "data" => json_encode($data)
    ]
]];

require_once('clientinfo.php');

$data = [
    "size" => [
        "width" => 1200,
        "height" => 810
    ],
    "selected" => true,
    "name" => "Nice boat.",
    "chatBarText" => "ゲームスタート！",
    "areas" => $area
];

$ch = curl_init('https://api.line.me/v2/bot/richmenu');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charser=UTF-8',
    'Authorization: Bearer '.$LINEINFO['access_token']
));
$result = curl_exec($ch);
//var_dump($result);
curl_close($ch);

$richMenuId = json_decode($result)->{"richMenuId"};
echo $richMenuId;


$file_name_with_full_path = 'RichMenu.png';

if (function_exists('curl_file_create')) { // php 5.5+
    $cFile = curl_file_create($file_name_with_full_path);
  } else { // 
    $cFile = '@' . realpath($file_name_with_full_path);
  }
// POST データを設定します
$path = './RichMenu.png';
/*
//$params = array('file'=> $cFile);
$params = array(
    'file'=>new CurlFile(realpath('RichMenu.png'),'image/png','image.png')
    );
var_dump($params);


$ch = curl_init('https://api-data.line.me/v2/bot/richmenu/'.$richMenuId.'/content');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: image/png',
    'Authorization: Bearer '.$LINEINFO['access_token']
));
$result = curl_exec($ch);
var_dump($result);
curl_close($ch);
*/
exec('curl -v -X POST https://api-data.line.me/v2/bot/richmenu/'.$richMenuId.'/content -H "Authorization: Bearer '.$LINEINFO['access_token'].'" -H "Content-Type: image/png" -T default.png');

$ch = curl_init('https://api.line.me/v2/bot/user/all/richmenu/'.$richMenuId);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer '.$LINEINFO['access_token']
));
$result = curl_exec($ch);
var_dump($result);
curl_close($ch);
