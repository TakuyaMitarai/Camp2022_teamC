<!DOCTYPE html>
<html lang ="ja">
    <head>
        <meta charset="utf-8" />
        <title>semester_schedule</title>
        <link rel = "stylesheet" href = "semester_schedule.css">
    </head>
    <body>
        <div class = "header">
            <img class = "inner1" id = "olabLogo" src = "./data/picture/otani.png"></img>
            <div class = "inner1">
                <p class = "inner2" id = "org" >東京都市大学 メディア情報学部 情報システム学科</p>
                <p class = "inner2" id = "lab">大谷研究室</p>
            </div>
        </div>
	<hr id = "hr1">
    <div class = "container">
        <p id = "title">大谷研究室 予定表ページ</p>
    </div>
    <img id = "aho" src="./data/picture/aho.png" alt = "aho"></img>
    <?php
    $yeard = 1;
    $nowYear1;
    if(date("m") >= 1 && date("m") <= 3) {
        $nowYear1 = date("Y");
    }else {
        $nowYear1 = date("Y") + 1;
    }
    $getYear = $nowYear1 - 1;
    
    if(!isset($_GET['Btn'])) {
        $errors[] = 'error7a';
    }elseif($_GET['Btn'] === '') {
        $errors[] = 'error7a';
    }else{
        if(!isset($_GET['years']) || $_GET['years'] === ''){
            $errors[] = 'Set year';
        }else{
            $getYear = $_GET['years'];
        }
    }

    //js準備
    $json_Syear = json_encode($getYear);
    $json_year = json_encode($nowYear1);
    ?>
<script>
    const js_Syear = JSON.parse('<?php echo $json_Syear; ?>');
    const js_year = JSON.parse('<?php echo $json_year; ?>');
</script>

<!--フォームから向こうまでプルダウン-->
    <form method="get" action="semester_schedule.php">
    <div class = "selectY">
    <select name="years" class="pu" id = "Yselect">
<?php
    $repNum = $nowYear1;
    for ($i=2014;$i<=$repNum;$i++){
        print('<option value='.$nowYear1.'>' . $nowYear1 . '</option>');
        $nowYear1 -= 1;
    }?>
    </select>
    <button type="submit" name="Btn" value= "years" class="btn--blue">変更</button>
    </div>
    </form>
    <!--ここまで-->


    <?php 
        $semester_column = array(); //学期の配列の初期化
        $grade_column; //学年の配列の初期化
        $jud_normal; //通常時の初回判定
        $RecentEventColor_semi = 0; //直近のゼミの印の初期化
        $pre_grade; //最初の学年の印
        $pre_normal = 1; //</table>の位置の処理
        $same_day;
        
        //現在の年月日取得
        $nowYear = date("Y");
        $nowMonth = date("m");
        $nowDay = date("d");
        $nowTime = $nowYear . $nowMonth . $nowDay;
        if($nowMonth >= 1 && $nowMonth <= 3) {
            $nowYear--;
        }
        $nowTime_timestamp = strtotime($nowTime);
        //パス作る
        $path = "./data/" . $getYear . "/schedule.csv";

        ?><div class="box1"><?php
        //csv読み込み(パスが合ってないとうまく動かない)
        $i = 0;
        $fp = fopen($path, 'r');
        while($data = fgetcsv($fp)){
            //$dataの処理
            $dataTable[$i][0] = $data[0];
            $semester = $dataTable[$i][0];

            $data[1] = str_replace('"', '', $data[1]);
            $dataTable[$i][1] = $data[1];
            $day = $nowYear . $dataTable[$i][1];
            $timestamp = strtotime($day);
            if(date('m', $timestamp) >= 1 && date('m', $timestamp) <= 3) {
                $day = $nowYear + 1 . $dataTable[$i][1];;
            }

            $dataTable[$i][2] = $data[2];
            $time = $dataTable[$i][2];
        
            $dataTable[$i][3] = $data[3];
            $content = $dataTable[$i][3];
            if(!(strcmp($content, "ゼミ")) && $i != 0){
                echo "</table>";
            }

            $dataTable[$i][4] = $data[4];
            $place = $dataTable[$i][4];
        
            $dataTable[$i][5] = $data[5];
            $target = $dataTable[$i][5];

            $i++;

            $jud01 = 1;
            foreach ($semester_column as $value) {
                if($semester == $value) {
                    //すでに学期が出力されているとき
                    $jud01 = 0;
                    break;
                }
            }
            if($jud01) {
                //学期の初回出力
                if($pre_normal == 0){
                    echo '</table>';
                    ?><br><hr><?php
                }
                array_push($semester_column, $semester);
                $grade_column = array(); //学年の配列の初期化
                $jud_normal = 1; //通常時の初回判定の初期化
                $jud_semi_print = 1; //ゼミ時の初回判定の初期化
                $RecentEventColor_normal = 0; //直近のスケジュールの印の初期化
                $pre_grade = 1; //初期化
            }
            schedule_print($semester, $day, $time, $content, $place, $target); //出力
        }
        ?></div><?php
        // ファイルを閉じる
        fclose($fp);
?>

    

<?php
        //出力関数
        function schedule_print($semester, $day, $time, $content, $place, $target)
        {
            global $jud_normal;
            global $grade_column;
            global $jud_semi_print;
            global $nowTime_timestamp;
            global $day_timestamp;
            global $nowYear;
            global $getYear;
            $day_timestamp = strtotime($day);
            $d = date('m', $day_timestamp);
            $m = date('d', $day_timestamp);
            $date = date('w', $day_timestamp);
            if($date == 0) {
                $date = '(日)';
            }else if($date == 1) {
                $date = '(月)';
            }else if($date == 2) {
                $date = '(火)';
            }else if($date == 3) {
                $date = '(水)';
            }else if($date == 4) {
                $date = '(木)';
            }else if($date == 5) {
                $date = '(金)';
            }else if($date == 6) {
                $date = '(土)';
            }
            global $RecentEventColor_semi; //直近のゼミの印
            global $RecentEventColor_normal; //直近のスケジュールの印;
            global $pre_grade;
            global $pre_normal;
            $jud = 1; //初回
            //変数さっぱりだったから作った。多分ある
            $thisMonth = date('m');
            $thisMonth ++;
            $thisMonth --;

            //contentが"ゼミ"の時の表示
            if(strcmp($content, "ゼミ") == 0) {
                if($jud_semi_print == 1) {
                    echo '<br><p class="se">' . $semester . 'ゼミ</p>'; ?> <br> <?php
                    ?> <div id = "pa"> <?php
                    $jud_semi_print = 0;
                }
                foreach($grade_column as $value) {
                    if($target == $value) {
                        $jud = 0;
                        //すでにカラムがある時の出力
                        $RecentEventColor_semi = RecentEvenetColor_semi($RecentEventColor_semi); //直近のゼミの色を変える
                        
                        if($RecentEventColor_semi == 1 && ((strcmp($nowYear,$getYear) == 0 && 4 <= $thisMonth && $thisMonth <= 12) || (strcmp($nowYear,$getYear) == 0 && 1 <= $thisMonth && $thisMonth <= 3))) {
                            ?><table class = "T T1"><?php
                            echo "<tr><td> $d 月 $m 日 $date</td><td class = \"Nperiod\"> $time </td></tr>";
                            echo "<tr><td> 場所: $place </td></tr>";
                            ?></table><?php
                            break;
                        }else {
                            ?><table class = "T"><?php
                            echo "<tr><td> $d 月 $m 日 $date</td><td class = \"Nperiod\"> $time </td></tr>";
                            echo "<tr><td> 場所: $place </td></tr>";
                            ?></table><?php
                            break;
                        }
                    }
                }
                if($jud == 1) {
                    if($pre_grade == 0) {
                        ?></div><?php
                    }
                    if($pre_grade == 1) {
                        $pre_grade = 0;
                    }
                    array_push($grade_column, $target);
                    //まだカラムがない時の出力
                    $RecentEventColor_semi = 0;  //初期化
                    $RecentEventColor_semi = RecentEvenetColor_semi($RecentEventColor_semi); //直近のゼミの色を変える
                    ?> <div id = "pi"> <?php
                    ?><table class = "T T2"><?php
                    echo '<tr><th class = "T2">' . $target . '</th></tr>';
                    ?></table><?php
                    ?><?php
                    if($RecentEventColor_semi == 1 && ((strcmp($nowYear,$getYear) == 0 && 4 <= $thisMonth && $thisMonth <= 12) || (strcmp($nowYear,$getYear) == 0 && 1 <= $thisMonth && $thisMonth <= 3))) {
                        ?><table class = "T T1"><?php
                        echo "<tr><td> $d 月 $m 日 $date</td><td class = \"Nperiod\"> $time </td></tr>";
                        echo "<tr><td> 場所: $place </td></tr>";
                    }else {
                        ?><table class = "T"><?php
                        echo "<tr><td> $d 月 $m 日 $date</td><td class = \"Nperiod\"> $time </td></tr>";
                        echo "<tr><td> 場所: $place </td></tr>";
                    }
                    ?></table><?php
                }
            }else {
                if($grade_column != null){
                    ?>
                    </div>
                    </div>
                    <?php
                }
                $pre_normal = 0;


                //contentが通常の時の表示
                if($jud_normal) {
                    //初回カラムの表示
                    $RecentEventColor_normal = RecentEvenetColor_normal($RecentEventColor_normal);
                    if($RecentEventColor_normal == 1 && ((strcmp($nowYear,$getYear) == 0 && 4 <= $thisMonth && $thisMonth <= 12) || (strcmp($nowYear,$getYear) == 0 && 1 <= $thisMonth && $thisMonth <= 3))) {
                        echo '<br><p id="ss">'. $semester . "スケジュール"; ?></caption><?php
                        echo '<table border="1" frame="box" class="T2">';?>
                        <tr><th><div class = "nom1">日付</div></th><th><div class = "nom1">時間</div></th><th><div class = "nom1">内容</div></th><th><div class = "nom1">場所</div></th><th><div class = "nom1">対象</div></th></tr> 
                        <?php
                        echo '<tr><td><div class = "nom2">' . $d . "月" . $m . "日" . $date . '</div></td><td><div class = "nom2">' . $time . '</div></td><td><div class = "nom2">' . $content . '</div></td><td><div class = "nom2">' . $place . '</div></td><td><div class = "nom2">' . $target . '</div>';?> </td></tr> <?php
                    }else {
                        echo '<br><p id="ss">'. $semester . "スケジュール"; ?></caption><?php
                        echo '<table border="1" frame="box" class="T2">';?>
                        <div class = "nom1">
                        <tr><th><div class = "nom1">日付</div></th><th><div class = "nom1">時間</div></th><th><div class = "nom1">内容</div></th><th><div class = "nom1">場所</div></th><th><div class = "nom1">対象</div></th></tr> 
                        </div> <?php
                        echo '<tr><td>' . $d . "月" . $m . "日" . $date . '</td><td>' . $time .'</td><td>' . $content . '</td><td>' . $place . '</td><td>' . $target;?> </td></tr> <?php
                    }
                    $jud_normal = 0;
                }else {
                    //通常日程表示
                    $RecentEventColor_normal = RecentEvenetColor_normal($RecentEventColor_normal);
                    if($RecentEventColor_normal == 1 && ((strcmp($nowYear,$getYear) == 0 && 4 <= $thisMonth && $thisMonth <= 12) || (strcmp($nowYear,$getYear) == 0 && 1 <= $thisMonth && $thisMonth <= 3))) {
                        echo '<tr><td><div class = "nom2">' . $d . "月" . $m . "日" . $date . '</div></td><td><div class = "nom2">' . $time . '</div></td><td><div class = "nom2">' . $content . '</div></td><td><div class = "nom2">' . $place . '</div></td><td><div class = "nom2">' . $target . '</div>';?> </td></tr> <?php
                    }else {
                        echo '<tr><td>' . $d . "月" . $m . "日" . $date . '</td><td>' . $time . '</td><td>' . $content . '</td><td>' . $place . '</td><td>' . $target;?> </td></tr> <?php
                    }
                }
            }
        }
        function RecentEvenetColor_semi($REC_semi)
        {
            global $nowTime_timestamp;
            global $day_timestamp;
            
            if($REC_semi == 0 && $nowTime_timestamp <= $day_timestamp) {
                return 1; //1で色を変える
            }else if ($REC_semi == 1) {
                return 2; 
            }else if ($REC_semi == 2){
                return 2;
            }else {
                return 0;
            }
        }

        function RecentEvenetColor_normal($REC_normal)
        {
            global $nowTime_timestamp;
            global $day_timestamp;
            global $same_day;
            global $day;
            if($REC_normal == 0 && $nowTime_timestamp <= $day_timestamp) {
                $same_day = $day;
                return 1; //1で色を変える
            }else if ($REC_normal == 1) {
                if($same_day == $day) {
                    return 1;
                }else {
                    return 2;
                }
            }else if ($REC_normal == 2){
                return 2;
            }else {
                return 0;
            }
        }
    ?> 
    </table>
    </div>
    </body>
    <footer>
        
    <table frame="box" id="Table">
        <caption>各時限の授業時間</caption>
        <tr>
            <td>1時限</td>
            <td align="right">9:20 ~ 11:00</td>
        </tr>
        <tr>
            <td>2時限</td>
            <td align="right">11:10 ~ 12:50</td>
        </tr>
        <tr>
            <td>3時限</td>
            <td align="right">13:40 ~ 15:20</td>
        </tr>
        <tr>
            <td>4時限</td>
            <td align="right">15:30 ~ 17:10</td>
        </tr>
        <tr>
            <td>5時限</td>
            <td align="right">17:20 ~ 19:00</td>
        </tr>
    </table>
    </footer>

    <script src = "semester_schedule.js"></script>
</html>
