<?php
require_once('clientinfo.php');
require_once('richmenu.php');
require_once('reversi.php');
/*
$LINEINFO['access_token'] 
$LINEINFO['channel_token'] 
$LINEINFO['channel_id'] 
*/
file_put_contents("test.txt",'ぼくうんち!');
$json_string = file_get_contents('php://input');
$json_object = json_decode($json_string);

$RECIEVEDATA['userid'] = $json_object->{"events"}[0]->{"source"}->{"userId"};
$RECIEVEDATA['replyToken'] = $json_object->{"events"}[0]->{"replyToken"};
$RECIEVEDATA['messageType'] = $json_object->{"events"}[0]->{"message"}->{"type"};
$RECIEVEDATA['recieveMessage'] = $json_object->{"events"}[0]->{"message"}->{"text"};
$RECIEVEDATA['recieveType'] = $json_object->{"events"}[0]->{"type"};
$RECIEVEDATA['postback'] = json_decode($json_object->{"events"}[0]->{"postback"}->{"data"}, true);

if($RECIEVEDATA['recieveMessage'] == "試合やめたい"){
    $messages = [[
        "type" => "template",
        "altText" => "試合をやめますか",
        "template" =>[
            "type" => "buttons",
            "text" => "試合をやめますか",
            "actions" => [
            [
                "type"=> "postback",
                "label"=> "しあいをやめる",
                "data"=> "reset",
            ]]
        ]
        ]];  

    file_put_contents("test.txt",json_encode($messages));
    $result = LINEReply($RECIEVEDATA['replyToken'] , $messages, $LINEINFO['access_token'] );
    file_put_contents("test.txt",json_encode($result));
    exit;
}
if($json_object->{"events"}[0]->{"postback"}->{"data"} == "reset"){
    ResetUser($LINEINFO, $RECIEVEDATA['userid']);
    if (file_exists($RECIEVEDATA['userid'].'.txt')){
        unlink($RECIEVEDATA['userid'].'.txt');
    }
    
    exit;
}


if (($RECIEVEDATA['recieveType'] == 'postback')and($RECIEVEDATA['postback']['status'] == 'start')){

    if (!file_exists($RECIEVEDATA['userid'].'.txt')){
        file_put_contents($RECIEVEDATA['userid'].'.txt', json_encode($board), LOCK_EX);
    }else{
        $board = json_decode(file_get_contents($RECIEVEDATA['userid'].'.txt'),true);
    }
    StorePlayerCanReverse(BLACK,$board);
    for ($row = 1; $row < SIZE - 1; $row++) {
        for ($col = 1; $col < SIZE - 1; $col++) {
            $posx = ($row-1) *100 +6;
            $posy = ($col-1) *100 +6;
            if ($board[$row][$col] == WHITE){
                $composite = $composite.' -composite WHITE.png -geometry +'.$posx.'+'.$posy.'';
            }
            if ($board[$row][$col] == BLACK){
                $composite = $composite.' -composite BLACK.png -geometry +'.$posx.'+'.$posy.'';
            }
        }
    }
    foreach($reverse_data_player as $value){
        $posx = ($value['row']-1) *100 +6;
        $posy = ($value['col']-1) *100 +6;
        $composite = $composite.' -composite Select.png -geometry +'.$posx.'+'.$posy.'';
    }

    $i=0;


    $area_total = [[
        "bounds" => [
            "x" => 910,
            "y" => 421,
            "width" => 199,
            "height" => 199
        ],
        "action" => [
            "type" => "message",
            "text" => "試合やめたい"
        ]
    ]];
    foreach($reverse_data_player as $value){
        $posx = ($value['row']-1) *100 +6;
        $posy = ($value['col']-1) *100 +6;

        $data = [
            "row" => $value['row'],
            "col" => $value['col'],
            "status" => "play"
        ];
        $area = [[
            "bounds" => [
                "x" => $posx,
                "y" => $posy,
                "width" => 100,
                "height" => 100
            ],
            "action" => [
                "type" => "postback",
                "data" => json_encode($data)
            ]
        ]];

        $area_total = array_merge($area_total, $area);

        if($i == 19) { 
            break;
        }
        $i++;
    }
    exec('convert -size 1200x810 xc:none BOARD.png -geometry +0+0 '.$composite.' -composite result_'.$RECIEVEDATA['userid'].'.png');
    changeRichMenu('いざバトル☆', $area_total, 'result_'.$RECIEVEDATA['userid'].'.png', $LINEINFO, $RECIEVEDATA['userid']);
    exec('rm result_'.$RECIEVEDATA['userid'].'.png');
    file_put_contents('boardtest.txt', json_encode($board), LOCK_EX);
    
    exit;
}

if (($RECIEVEDATA['recieveType'] == 'postback')and($RECIEVEDATA['postback']['status'] == 'play')){
    if (!file_exists($RECIEVEDATA['userid'].'.txt')){
        file_put_contents($RECIEVEDATA['userid'].'.txt', json_encode($board), LOCK_EX);
    }else{
        $board = json_decode(file_get_contents($RECIEVEDATA['userid'].'.txt'),true);
    }

    if (CountReverseAll($RECIEVEDATA['postback']['row'], $RECIEVEDATA['postback']['col'], BLACK, 1, $board) == 0){
        $messages = [[
            "type" => "text",
            "text" => "無効な手です"
        ]];
        $result = LINEReply($RECIEVEDATA['replyToken'] , $messages, $LINEINFO['access_token'] ); 
        exit;
    }

    
    file_put_contents("test.txt","isee");
    $pass_white = PCPhase(WHITE, $board);
    file_put_contents("test.txt","isee2");
    StorePlayerCanReverse(BLACK,$board);
    file_put_contents($RECIEVEDATA['userid'].'.txt', json_encode($board), LOCK_EX);

    file_put_contents("test.txt","isee3");


    /* convert -size 440x370 xc:none \
        umaru.png -geometry +210+110 -composite \
        saber_rotated.png -geometry +0+0 -composite \
        result.png */
    for ($row = 1; $row < SIZE - 1; $row++) {
        for ($col = 1; $col < SIZE - 1; $col++) {
            $posx = ($row-1) *100 +6;
            $posy = ($col-1) *100 +6;
            if ($board[$row][$col] == WHITE){
                $composite = $composite.' -composite WHITE.png -geometry +'.$posx.'+'.$posy.'';
            }
            if ($board[$row][$col] == BLACK){
                $composite = $composite.' -composite BLACK.png -geometry +'.$posx.'+'.$posy.'';
            }
        }
    }
    foreach($reverse_data_player as $value){
        $posx = ($value['row']-1) *100 +6;
        $posy = ($value['col']-1) *100 +6;
        $composite = $composite.' -composite Select.png -geometry +'.$posx.'+'.$posy.'';
    }

    $i=0;


    $area_total = [[
        "bounds" => [
            "x" => 910,
            "y" => 421,
            "width" => 199,
            "height" => 199
        ],
        "action" => [
            "type" => "message",
            "text" => "試合やめたい"
        ]
    ]];
    foreach($reverse_data_player as $value){
        $posx = ($value['row']-1) *100 +6;
        $posy = ($value['col']-1) *100 +6;

        $data = [
            "row" => $value['row'],
            "col" => $value['col'],
            "status" => "play"
        ];
        $area = [[
            "bounds" => [
                "x" => $posx,
                "y" => $posy,
                "width" => 100,
                "height" => 100
            ],
            "action" => [
                "type" => "postback",
                "data" => json_encode($data)
            ]
        ]];

        $area_total = array_merge($area_total, $area);

        if($i == 19) { 
            break;
        }
        $i++;
    }
    //file_put_contents("test.txt",json_encode($area_total));

    if(($blackcanput = CountCanPut(BLACK,$board)) == 0){
        while(1){
            if((($whitecanput = CountCanPut(WHITE,$board)) == 0)||(($blackcanput = CountCanPut(BLACK,$board)) != 0)){
                break;
            }
            PCPhase(WHITE, $board);
        }
    }

    if((($blackcanput = CountCanPut(BLACK,$board)) == 0)&&(($whitecanput = CountCanPut(WHITE,$board)) == 0)){
        unlink($RECIEVEDATA['userid'].'.txt');
        if(CountHowMany(BLACK,$board) > CountHowMany(WHITE,$board)){
            $JapaneseResult = '●黒の勝ち！';
            $addMusic = [
                [
                    "type"=>"text",
                    "text"=>"(勝利のBGM)"
                ]];
        }
        if(CountHowMany(BLACK,$board) < CountHowMany(WHITE,$board)){
            $JapaneseResult = '○白の勝ち！';
            $addMusic = [[
                "type"=>"text",
                "text"=>"(敗北のBGM)"
            ]];
        }
        if(CountHowMany(BLACK,$board) == CountHowMany(WHITE,$board)){
            $JapaneseResult = '引き分け！';
        }

        $ResultCSS = [[
            "type"=> "flex",
            "altText"=> "●".CountHowMany(BLACK,$board)." - ".CountHowMany(WHITE,$board)."○ で".$JapaneseResult,
            "contents"=> [
                "type"=> "bubble",
                "body"=> [
                    "type"=> "box",
                    "layout"=> "vertical",
                    "contents"=> [
                        [
                        "type"=> "text",
                        "text"=> "●".CountHowMany(BLACK,$board)." - ".CountHowMany(WHITE,$board)."○",
                        "align"=> "center",
                        "size"=> "4xl"
                        ],
                        [
                        "type"=> "text",
                        "text"=> $JapaneseResult,
                        "align"=> "center",
                        "weight"=> "bold",
                        "size"=> "xl"
                        ]
                    ]
                    ]
                ]
        ]
        ];
        if (isset($addMusic)){
            $ResultCSS = array_merge($addMusic,$ResultCSS);
        }
        $composite = $composite.' -composite NextBatle.png -geometry +808+154';
        file_put_contents("testcss.txt",json_encode($ResultCSS));
        $result = LINEReply($RECIEVEDATA['replyToken'] , $ResultCSS, $LINEINFO['access_token'] );
        file_put_contents("testresult.txt",json_encode($result));

    }

    exec('convert -size 1200x810 xc:none BOARD.png -geometry +0+0 '.$composite.' -composite result_'.$RECIEVEDATA['userid'].'.png');
    changeRichMenu('いざバトル☆', $area_total, 'result_'.$RECIEVEDATA['userid'].'.png', $LINEINFO, $RECIEVEDATA['userid']);
    exec('rm result_'.$RECIEVEDATA['userid'].'.png');
}
/*
$result = LINEReply($RECIEVEDATA['replyToken'] ,$messages, $LINEINFO['access_token'] );

file_put_contents("test.txt",json_encode($result));
*/
function LINEReply ($replyToken, $messages, $accessToken){

    $data = [
        "replyToken" => $replyToken,
        "messages" => $messages
    ];

    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer '.$accessToken
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}