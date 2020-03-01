<?php 

define('BLACK','1');
define('WHITE','2');
define('NONE','0');
define('SIZE',8+2);

function CalcEvaluation($my_color,&$board) {
    // [要変更] 四隅を最優先、一番外側を次に優先
    $eval_board = [
        [ 0,   0,   0,   0,   0,   0,   0,   0,   0,   0 ],
        [ 0, 100,   5,   5,   5,   5,   5,   5, 100,   0 ],
        [ 0,   5,   1,   1,   1,   1,   1,   1,   5,   0 ],
        [ 0,   5,   1,   1,   1,   1,   1,   1,   5,   0 ],
        [ 0,   5,   1,   1,   1,   1,   1,   1,   5,   0 ],
        [ 0,   5,   1,   1,   1,   1,   1,   1,   5,   0 ],
        [ 0,   5,   1,   1,   1,   1,   1,   1,   5,   0 ],
        [ 0,   5,   1,   1,   1,   1,   1,   1,   5,   0 ],
        [ 0, 100,   5,   5,   5,   5,   5,   5, 100,   0 ],
        [ 0,   0,   0,   0,   0,   0,   0,   0,   0,   0 ]
    ];

  /* int eval_board[SIZE][SIZE] = {
  {0,0,0,0,0,0,0,0,0,0},
  {0,120, -20,  20,   5,   5,  20, -20, 120,0},
  {0,-20, -40,  -5,  -5,  -5,  -5, -40, -20,0},
  {0,20,  -5,  15,   3,   3,  15,  -5,  20,0},
  {0,5,  -5,   3,   3,   3,   3,  -5,   5,0},
  {0, 5,  -5,   3,   3,   3,   3,  -5,   5,0},
  {0, 20,  -5,  15,   3,   3,  15,  -5,  20,0},
  {0,-20, -40,  -5,  -5,  -5,  -5, -40, -20,0},
  {0,120, -20,  20,   5,   5,  20, -20, 120,0},
  {0,0,0,0,0,0,0,0,0,0} 
   };
   */
    $my_value = 0;	    // 自分の評価値
    $enemy_value = 0;    // 相手の評価値


    // 相手の石を設定
    if ($my_color == WHITE) {
        $enemy_color = BLACK;
    }
    else {
        $enemy_color = WHITE;
    }

    //評価値を計算
    for ($row = 1; $row < SIZE - 1; $row++) {
        for ($col = 1; $col < SIZE - 1; $col++) {
            // [要作成：複数行] 現在の場所が自分の石の場合は、自分の評価値に現在の場所の評価値を加える
            if ($board[$row][$col] == $my_color) {
                $my_value += $eval_board[$row][$col];
                //printf("My_Value! %d eval %d\n", my_value, eval_board[row][col]);
            }
            // [要作成：複数行] 現在の場所が相手の石の場合は、相手の評価値に現在の場所の評価値を加える
            if ($board[$row][$col] == $enemy_color) {
                $enemy_value += $eval_board[$row][$col];
                //printf("Enemy_Value! %d eval %d\n", enemy_value, eval_board[row][col]);
            }
        }
    }

    // [要作成](自分の評価値-相手の評価値)を返す 
    return $my_value - $enemy_value;
}

function StoreReverse($put_color, &$board) {
    global $reverse_data;
    $reverse_data_count = 0; // 石を置ける場所の数

    for ($col = 1; $col < SIZE - 1; $col++) {
        for ($row = 1; $row < SIZE - 1; $row++) {
            // [要作成：複数行] Countreverseall関数を呼び出して裏返せる数をreverse_countに代入する
            $reverse_count = CountReverseAll($row, $col, $put_color, 0, $board);
            //                 reverse_countが1以上の場合はrow, col, reverse_count を構造体に保存し、reverse_data_countを1増やす
            if ($reverse_count >= 1) {
                $reverse_data['row'] = $row;
                $reverse_data['col'] = $col;
                $reverse_data['reverse_count'] = $reverse_count;
                $reverse_data_count += 1;
            }
        }
    }
    // [要作成] reverse_data_count を返す
    return $reverse_data_count;
}

function StorePlayerCanReverse($put_color, &$board) {
    global $reverse_data_player;
    $reverse_data_count = 0; // 石を置ける場所の数

    for ($col = 1; $col < SIZE - 1; $col++) {
        for ($row = 1; $row < SIZE - 1; $row++) {
            // [要作成：複数行] Countreverseall関数を呼び出して裏返せる数をreverse_countに代入する
            $reverse_count = CountReverseAll($row, $col, $put_color, 0, $board);
            //                 reverse_countが1以上の場合はrow, col, reverse_count を構造体に保存し、reverse_data_countを1増やす
            if ($reverse_count >= 1) {
                $reverse_data_player[$reverse_data_count]['row'] = $row;
                $reverse_data_player[$reverse_data_count]['col'] = $col;
                $reverse_data_player[$reverse_data_count]['reverse_count'] = $reverse_count;
                $reverse_data_count += 1;
            }
        }
    }
    // [要作成] reverse_data_count を返す
    return $reverse_data_count;
}

function PCPhase($put_color, &$board) {
    $reverse_data_count = 0;         // 置ける場所の数

    // [要作成] どちらの石かを表示
    printf("PC:%cです\n", $put_color);
    // [要作成] CountCanPut関数を呼び出して、石が置ける場所の数を取得し、reverse_data_countへ格納
    $reverse_data_count = CountCanPut($put_color, $board);
    // 置ける場所の数が0の場合はパス（1を返す）
    if ($reverse_data_count == 0) {
        printf("pass\n");				//パスの表示
        //printf("Press Enter key\n");	//エンターキーの入力待ちメッセージ
        //while (getchar() != '\n');		//エンターキー入力待ち
        // [要作成] 1を返す（パス）
        return 1;
}
// 置ける場所がある場合は石を置く（0を返す）
    else {

    // [要作成] PCBestEvaluation関数を呼び出して、PCの打ち手の評価値を計算して、一番高いものを選択
        PCBestEvaluation($reverse_data_count, $put_color, $board);
        return 0;
    }
}
function HumanPhase($put_color, &$board, $auto_play) {

    // [要作成] どちらの石かを表示
    printf("次のターン:%c\n", $put_color);
    // [要作成] StoreReverse関数を呼び出して、石が置ける場所の数を取得し、reverse_data_countへ代入
    $reverse_data_count= StoreReverse($put_color, $board);
    StorePlayerCanReverse($put_color, $board);
    //置ける場所がない場合（石が置ける場所の数が0）はパス
    if ($reverse_data_count == 0) {
        printf("pass\n");               //パスの表示
        return 1;
    }
    // 裏返せる場所がある場合
    else {
        // [要作成：複数行] 人の打ち手を入力し、置ける場合は石を置く。置けない場合は置けるまで入力からやり直す。
        do {
            if ($auto_play == 0) {
                do {
                    printf("行を指定:"); 
                    $put_row = trim(fgets(STDIN)); 
                    if ($put_row < 1 || $put_row > 8) {
                        printf("無効な値です\n");
                    }
                } while ($put_row < 1 || $put_row > 8);
                $row = $put_row;
                do {
                     printf("列を指定:");
                     $put_col= trim(fgets(STDIN)); 
                    if (!(($put_col >= 'A') && ('H' >= $put_col))){
                        printf("無効な値です\n");
                    }
                } while (!(($put_col >= 'A') && ('H' >= $put_col)));
                $col = ord($put_col) - ord('A') + 1;
 
            }
            printf("%d,%d\n", $row, $col);
            if (($reverse_count = CountReverseAll($row, $col, $put_color, 1, $board)) == 0)
                printf("そこは置けません\n");
        } while ($reverse_count == 0);

    }


    // [要作成] 0を返す（石を置いた）
    return 0;
}

function PCBestEvaluation($reverse_data_count, $put_color, &$board) {
    global $composite;
    // [要作成] StoreReverseMalloc関数を呼び出して、置ける場所と裏返せる個数を取得し、reverse_dataへ格納
    $reverse_data = StoreReverseMalloc($reverse_data_count, $put_color, $board);
    // [要作成] REVERSE_DATA構造体をreverse_data_countの数だけメモリを確保して、my_evalに確保した領域の先頭アドレスを格納
   
    for ($data_number = 0; $data_number < $reverse_data_count; $data_number++) {
        // [要作成] CopyBoard関数を呼び出して、実際の盤面に置いてしまわないようにboardをtmp_boardへコピーする（この中ではコピーしたtmp_boardを使用する）
        $tmp_board = $board;
        // [要作成] CountReverseAll関数を呼び出して（isPutは1）、tmp_boardに石を置いてみる
        CountReverseAll($reverse_data[$data_number]['row'], $reverse_data[$data_number]['col'], $put_color, 1, $tmp_board);
        // [要作成] 置いてみたときの行の位置をmy_evalへ格納
        $my_eval[$data_number] = [
            "row" => $reverse_data[$data_number]['row'],
            "col" => $reverse_data[$data_number]['col'],
            "reverse_count" =>  CalcEvaluation($put_color,$tmp_board)
        ];
    }


    // [要作成：複数行] 評価値をもとにmy_eval配列を降順にソート（データ数はreverse_data_count）
    for ($i = 0; $i < $reverse_data_count; $i++) {
        for ($j = $reverse_data_count - 1; $j > $i; $j--) {
            if ($my_eval[$j]['reverse_count'] > $my_eval[$j - 1]['reverse_count']) {
                $tmp = $my_eval[$j];
                $my_eval[$j] = $my_eval[$j - 1];
                $my_eval[$j - 1] = $tmp;
            }
        }
    }
    


    $select = 0; // 1番評価値の大きなデータを選ぶ（同じ評価値の場合は最初のデータ）
    // [要作成] CountReverseAll関数を呼び出し（isPutは1）、my_eval[select]に格納してあるrow,colの位置に石を置く(実際に置くのでboardに置く)
    CountReverseAll($my_eval[$select]['row'], $my_eval[$select]['col'], $put_color, 1, $board);
    $posx = ($my_eval[$select]['row']-1) *100 +6;
    $posy = ($my_eval[$select]['col']-1) *100 +6;
    $composite = $composite.' -composite EnemySelect.png -geometry +'.$posx.'+'.$posy.'';
    // [要作成] 選択した場所を表示（3Dのように表示）
    $printCol='A';
    for($i; $i <= $my_eval[$select]['col'] - 1; $i++){
        $printCol++;
    }

    printf("PC:%d%sを選択\n", $my_eval[$select]['row'], $printCol);
   // printf("Press Enter key\n");	// エンターキーを押してもらう
   // while (getchar() != '\n');		// エンターキー入力待ち

}

function StoreReverseMalloc($reverse_data_count, $put_color, &$board) {
    $data_number = 0;    // 配列の要素番号
    global $reverse_data;
    // [要作成] REVERSE_DATA構造体をreverse_data_countの数だけメモリを確保して、reverse_dataに確保した領域の先頭アドレスを格納
    // Countreverseall関数を呼び出して、裏返せる個数が1以上の場合はreverse_data_countを1増やす
    for ($row = 1; $row < SIZE - 1; $row++) {
        for ($col = 1; $col < SIZE - 1; $col++) {
            // [要作成：複数行] CountReverseAll関数を呼び出して(isPutは0)、0よりも大きな数が返ってきたら、現在の行・列・裏返せる個数をreverse_data配列のdata_number番目に格納し、data_numberを1増やす
            //printf("%d,%d", row, col);
           
            if (($reverse_count = CountReverseAll($row, $col, $put_color, 0, $board)) > 0) {
                $reverse_data_tmp = [[
                    "row" => $row,
                    "col" => $col,
                    "data_number" => $reverse_count
                ]];
                $data_number++;
                if ($data_number == 1){
                    $reverse_data = $reverse_data_tmp;
                } else {
                    $reverse_data = array_merge($reverse_data , $reverse_data_tmp);
                }
            }
        }
    }

    // [要作成] 確保した配列の先頭アドレスを返す
    return $reverse_data;
}

function CountCanPut($put_color, &$board) {
    $reverse_data_count = 0;	// 置ける場所の数

    // 置ける場所を検索
    for ($row = 1; $row < SIZE - 1; $row++) {
        for ($col = 1; $col < SIZE - 1; $col++) {
            if (CountReverseAll($row, $col, $put_color, 0, $board) > 0)
                $reverse_data_count++;
        }
    }

    // [要作成] 置ける場所の数を返す
    return $reverse_data_count;
}

function CountHowMany($put_color, &$board) {
    $reverse_data_count = 0;	// 置ける場所の数

    // 置ける場所を検索
    for ($row = 1; $row < SIZE - 1; $row++) {
        for ($col = 1; $col < SIZE - 1; $col++) {
            if ($board[$row][$col] == $put_color)
                $reverse_data_count++;
        }
    }

    // [要作成] 置ける場所の数を返す
    return $reverse_data_count;
}

function CountReverseAll($put_row, $put_col, $put_color, $isPut, &$board){

    if ($board[$put_row][$put_col] != NONE){
        return 0;
    }
    if ($put_color == BLACK){
        $reverse_color = WHITE;
    } else {
        $reverse_color = BLACK;
    }
    $reverse_number_total = 0;
    for ($vec_row=-1; $vec_row<=1; $vec_row++){
        for ($vec_col=-1; $vec_col<=1; $vec_col++){
            $reverse_number_total += CountReverse($put_row, $put_col,$put_color, $reverse_color, $vec_row, $vec_col, $isPut, $board);
        }
    }

    return $reverse_number_total;
}

function CountReverse($put_row, $put_col, $put_color, $reverse_color, $vec_row, $vec_col, $isPut, &$board) {
    $row = $put_row + $vec_row;    // 石を置いた位置の隣からスタート（行方向の現在位置とする）
    $col = $put_col + $vec_col;    // 石を置いた位置の隣からスタート（列方向の現在位置とする）
    $reverse_number = 0;         // 裏返せる個数をリセット
    while ($board[$row][$col] == $reverse_color) {
        /* [要作成]裏返せる個数を1増やす */
        $reverse_number += 1;
        /* [要作成]隣の石へ位置を進める（行方向）*/
        $row += $vec_row;
        /* [要作成]隣の石へ位置を進める（列方向）*/
        $col += $vec_col;
    }

    if ($board[$row][$col] == $put_color) {

        // [後から追加分 要作成]裏返し処理をする場合（isPutが1でreverse_numberが1以上の場合）
        if (($isPut == 1) && ($reverse_number >= 1)) {
            // [後から追加分 要作成]1つ戻る（行方向）
            $row -= $vec_row;
            // [後から追加分 要作成]1つ戻る（列方向）
            $col -= $vec_col;
            // [後から追加分 要作成　複数行]現在の位置が反対の石の間、自分の石に裏返し、行方向と列方向を1つ戻す
            while ($board[$row][$col] == $reverse_color) {
                $board[$row][$col] = $put_color;
                $row -= $vec_row;
                $col -= $vec_col;
            }

            // [後から追加分 要作成]裏返し処理終了後に、現在の場所に石を置く
            $board[$row][$col] = $put_color;
        }

        return $reverse_number;
    }

}
function PrintBoard($board) {
    $row_start_label = 1;
    $col_start_label = ord('A');
    printf("%s", ' ');
    for ($col = 1; $col < SIZE - 1; $col++) {
        printf(" ");
        printf(" %c ", $col_start_label + $col - 1);
    }
    printf("\n");
    //printf("\\\\\n");
    for ($row = 1; $row < SIZE; $row++) {
        printf(" ");
        for ($col = 1; $col < SIZE - 1; $col++) {
            printf("+");
            printf("---");
        }
        printf("+\n");
        // printf(" +---+---+---+---+---+---+---+---+\n");
         //printf("\\\\\n");
        if ($row == SIZE - 1)
            return;
        printf("%d", $row);
        for ($col = 1;$col < SIZE; $col++) {
            printf("|");
            printf("%2s ", $board[$row][$col]);
        }

        printf("\n");
        // printf("\\\\\n");
    }
}

function InitBoard($board) {
    $row_start_label = 1; // 行方向の最初のラベル
    $col_start_label = ord('A'); // 列方向の最初のラベル
    

    /* [要作成：複数行]boardの中身をいったんすべてEMPTYにする */
    for ($row = 0; $row < SIZE; $row++) {
        for ($col = 0; $col < SIZE; $col++) {
            //printf("%d,%d\n", row, col);
            $board[$row][$col] = NONE;
        }
    }


    /* [要作成：複数行]boardの中央部分に白と黒の石を2個ずつ配置する*/
    /* 白 黒 */
    /* 黒 白 */
    $board[(SIZE - 2) / 2][(SIZE - 2) / 2] = WHITE;
    $board[(SIZE - 2) / 2][(SIZE - 2) / 2 + 1] = BLACK;
    $board[(SIZE - 2) / 2 + 1][(SIZE - 2) / 2] = BLACK;
    $board[(SIZE - 2) / 2 + 1][(SIZE - 2) / 2 + 1] = WHITE;

    return $board;
}
    $reverse_data_player = array();
    $board = array();
    $board = InitBoard($board);
    //PrintBoard($board);
    //StorePlayerCanReverse(BLACK,$board);
     /*
    printf("先手(%c)(0:PLAYER 1:CPU):",BLACK); 
    $FIRST =  0;
    printf("後手(%c)(0:PLAYER 1:CPU):",WHITE); 
    $SECOND = 1;

    while (1) {
        // 黒の手番として、人の手番を入力する関数を呼び出し、結果をpass_blackに代入
        if ($FIRST == 1) {
            $pass_black = PCPhase(BLACK, $board);
        }
        else {
            $pass_black = HumanPhase(BLACK, $board, 0);
        }
        // 盤面表示関数を呼び出す
        //system("cls");
        PrintBoard($board);
        // pass_blackとpass_whiteが両方1（パス）ならばbreak;
        if ($pass_black == 1 && $pass_white == 1)
            break;

        // 白の手番として、人の手番を入力する関数を呼び出し、結果をpass_whiteに代入
        if ($SECOND == 1) {
            $pass_white = PCPhase(WHITE, $board);
        }
        else {
            $pass_white = HumanPhase(WHITE, $board, 0);
        }
        // 盤面表示関数を呼び出す
        //system("cls");
        PrintBoard($board);
        // pass_blackとpass_whiteが両方1（パス）ならばbreak;
        if ($pass_black == 1 && $pass_white == 1)
            break;

    }

    // CheckResult関数を呼び出す
    //CheckResult(board);
*/
    
