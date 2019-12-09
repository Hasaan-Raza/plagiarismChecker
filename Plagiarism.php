<?php
$result2 = $result1 = "";
$check = FALSE;
$plag_count = 0;
$per_1 = $per_2 = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_file']) && $_POST['submit_file'] == "submit_file") {
        $fileName1 = $_POST['file_1'];
        $fileName2 = $_POST['file_2'];

        $nameLength1 = strlen($fileName1);
        $nameLength2 = strlen($fileName2);

        if (($nameLength1 > 4 && substr($fileName1, $nameLength1 - 4) == '.txt') && ($nameLength2 > 4 && substr($fileName2, $nameLength2 - 4) == '.txt')) {
            $check = TRUE;
            $a = $_POST['file_1'];
            $b = $_POST['file_2'];
            $count = $_POST['words'];
            $myfile1 = fopen("$a", "r") or die("Unable to open file!");
            $myfile2 = fopen("$b", "r") or die("Unable to open file!");

            $result1 = fread($myfile1, filesize("$a"));
            $result2 = fread($myfile2, filesize("$b"));

            $arr1 = explode('.', $result1);
            $arr2 = explode('.', $result2);

            $len_1 = count($arr1);
            $len_2 = count($arr2);

            $min_length = min($len_1, $len_2);
            $arr = array();
            for ($i = 0; $i < $min_length; $i++) {
                $result = compare_string($arr1[$i], $arr2[$i], $count);
                $arr[$i] = $result;

                //print_r($arr);
            }

            fclose($myfile1);
            fclose($myfile2);
        } else {
            echo "Only select txt file.";
        }
    } else if (isset($_POST['submit_text']) && $_POST['submit_text'] == "submit_text") {
        $check = TRUE;
        $result1 = $_POST['text_1'];
        $result2 = $_POST['text_2'];
        $count = $_POST['words'];
        //echo $count;
        $arr1 = explode('.', $result1);
        $arr2 = explode('.', $result2);
        $len_1 = count($arr1);
        $len_2 = count($arr2);
        //$result = array();
        $min_length = min($len_1, $len_2);
        //echo $min_length;
        $arr = array();
        for ($i = 0; $i < $min_length - 1; $i++) {
            $result = compare_string($arr1[$i], $arr2[$i], $count);
            $arr[$i] = $result;
            if ($result != 1)
                $plag_count++;
            //print_r($arr);
        }
    } else if (isset($_POST['submit_multiple']) && $_POST['submit_multiple'] == "submit_multiple") {
        $countfiles = count($_FILES['file']['name']);
        for ($i = 0; $i < $countfiles; $i++) {
            $filename = $_FILES['file']['name'][$i];
            $nameLength = strlen($filename);
            if (($nameLength > 4 && substr($filename, $nameLength - 4) == '.txt')) {
                move_uploaded_file($_FILES['file']['tmp_name'][$i], 'upload/' . $filename);
            }
        }
    }
}

function compare_string($s1, $s2, $num)
{
    if ($s1 == "" || $s2 == "") return;
    $test1 = explode(' ', $s1);
    $test2 = explode(' ', $s2);
    $size_1 = count($test1);
    $size_2 = count($test2);
    //echo $size_1 . $size_2;
    $words_limit = 0;
    $index_sent1 = 0;
    $index_sent2 = 0;
    $index_sent3 = 0;
    $index_sent4 = 0;
    $flag = TRUE;
    for ($i = 0; $i < $size_1; $i++) {
        for ($j = 0; $j < $size_2; $j++) {
            lbl: if ($i < $size_1 && $j < $size_2 && $test1[$i] == $test2[$j] && ($test1[$i] != "" || $test2[$j] != "")) {
                if ($words_limit == 0) {
                    $index_sent1 = $i;
                    $index_sent2 = $j;
                }
                $words_limit++;
                $i++;
                $j++;
                if ($words_limit >= $num) {
                    $flag = FALSE;
                    $x = $i;
                    $y = $j;
                    $index_sent3 = $x - 1;
                    $index_sent4 = $y - 1;
                    //break;
                }
                goto lbl;
            } elseif ($flag == FALSE) {
                break;
            } else {
                $i -= $words_limit;
                $words_limit = 0;
            }
        }
        //if ($flag == FALSE) break;
    }
    if ($flag == TRUE)
        return TRUE;
    else if ($flag == FALSE)
        return array($index_sent1, $index_sent3, $index_sent2, $index_sent4);
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Plagiarism Checker</title>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <style>
        #choose_file,
        #choose_text {
            padding-top: 10px;
        }

        .words_limit {
            width: 100%;
            padding-bottom: 20px;
        }

        .none {
            display: none;
        }

        .showDIV {
            display: block;
        }

        .container {
            width: 59.4%;
            max-width: 1200px;
            margin: 0 auto;
        }

        #btn {
            float: right;
            margin-right: 50px;
            margin-top: 10px;
        }

        span {
            color: red;
        }

        .show_result {
            width: 47%;
            float: left;
        }

        #first_result {
            border-right: 1px solid black;
            margin-right: 10px;
        }

        .bar {
            margin-top: 16px;
            margin-bottom: 16px;
            color: #fff;
            background-color: #f44336;
            text-align: center;
        }
    </style>
    <script>
        function showFile() {
            $("#choose_file").removeClass("none");
            $("#choose_file").addClass("showDIV");
            $("#choose_text").removeClass("showDIV");
            $("#choose_text").addClass("none");
            $("#choose_Multiple_file").removeClass("showDIV");
            $("#choose_Multiple_file").addClass("none");
        }

        function showText() {
            $("#choose_text").removeClass("none");
            $("#choose_text").addClass("showDIV");
            $("#choose_file").removeClass("showDIV");
            $("#choose_file").addClass("none");
            $("#choose_Multiple_file").removeClass("showDIV");
            $("#choose_Multiple_file").addClass("none");
        }

        function showMultipleFile() {
            $("#choose_Multiple_file").removeClass("none");
            $("#choose_Multiple_file").addClass("showDiv");
            $("#choose_text").removeClass("showDiv");
            $("#choose_text").addClass("none");
            $("#choose_file").removeClass("showDIV");
            $("#choose_file").addClass("none");
        }
    </script>
</head>

<body>
    <div>
        <div class="container">
            <h2 style="margin-left: 30%;">Plagiarism Checker</h2>
            <h3>How do you want to check Plagiarism?</h3>
            <input type="radio" name='thing' value='valuable' onclick="showFile()" />
            <label>By Choosing File</label>
            <input type="radio" name='thing' value='valuable' onclick="showText()" />
            <label>By Writing Text</label>
            <input type="radio" name='thing' value='valuable' onclick="showMultipleFile()" />
            <label>By Choosing Multiple file</label>
        </div>
    </div>
    <div id="choose_file" class="none">
        <div class="container">
            <form method="POST" action="">
                <div class="words_limit" style="width:100%;">
                    <h3>Set your words for Plagiarism Checking.</h3>
                    <input type="radio" id="myRadio" name='words' value='3' checked />
                    <label>3 Words</label>
                    <input type="radio" id="myRadio" name='words' value='5' />
                    <label>5 Words</label>
                </div>
                <input type="file" name="file_1" value="File 1">
                <input type="file" name="file_2" value="File 2">
                <input type="submit" name="submit_file" value="submit_file">
            </form>
        </div>
    </div>
    <div id="choose_text" class="none">
        <div class="container">
            <form method="POST" action="">
                <div class="words_limit" style="width:100%;">
                    <h3>Set minimum words for Plagiarism Checking.</h3>
                    <input type="radio" name='words' value='3' checked />
                    <label>3 Words</label>
                    <input type="radio" name='words' value='5' />
                    <label>5 Words</label>
                </div>
                <textarea style="resize: none; outline: none;" rows="10" cols="50" name="text_1" placeholder="Enter text here" required></textarea>
                <textarea style="resize: none; outline: none;" rows="10" cols="50" name="text_2" placeholder="Enter text here" required></textarea><br>
                <input id="btn" type="submit" name="submit_text" value="submit_text">
            </form>
        </div>
    </div>
    <div id="choose_Multiple_file" class="none">
        <div class="container">

            <form method='post' action='' enctype='multipart/form-data'>
                <div class="words_limit" style="width:100%;">
                    <h3>Set your words for Plagiarism Checking.</h3>
                    <input type="radio" id="myRadio" name='words' value='3' checked />
                    <label>3 Words</label>
                    <input type="radio" id="myRadio" name='words' value='5' />
                    <label>5 Words</label>
                </div>
                <input type="file" name="file[]" id="file" multiple>
                <input type='submit' name='submit_multiple' value='submit_multiple'>
            </form>
        </div>
    </div>
    <!--result-->
    <div>
        <div class="container">
            <h2>Result:</h2>
            <div class="show_result" id="first_result">
                <?php
                if ($min_length - 1 == $len_1 - 1)
                    $per_1 = (int) (($plag_count / ($min_length - 1)) * 100);
                else
                    $per_1 = (int) (($plag_count / ($len_1 - 1)) * 100);
                ?>
                <div class="bar" style="width:<?php echo $per_1; ?>%"><?php echo $per_1; ?>%</div>
                <p>
                    <?php
                    //echo $result1;
                    if ($check == TRUE) {
                        for ($i = 0; $i < $min_length - 1; $i++) {
                            if ($arr[$i] == 1) {
                                echo $arr1[$i];
                                echo " ";
                            } elseif ($arr[$i] != 1) {
                                //echo $arr[$i];
                                $start_end = array();
                                $start_end = $arr[$i];
                                $start_index = $start_end[0];
                                $end_index = $start_end[1];
                                $arrayOfSingleSentence = explode(' ', $arr1[$i]);
                                for ($j = 0; $j < count($arrayOfSingleSentence); $j++) {
                                    if ($start_index == $j) {
                                        for ($z = $start_index; $z <= $end_index; $z++) {
                                            echo "<span> $arrayOfSingleSentence[$z] </span>";
                                        }
                                        $j = $end_index;
                                    } elseif ($start_index != $j) {
                                        echo $arrayOfSingleSentence[$j];
                                        echo " ";
                                    }
                                }
                            }
                            echo ".";
                            //echo "<br>";
                        }
                        if ($min_length != $len_1) {
                            for ($k = $min_length - 1; $k < $len_1; $k++) {
                                echo $arr1[$k];
                                echo " ";
                            }
                        }
                    }
                    ?>
                </p>
            </div>
            <div class="show_result">
                <?php
                if ($min_length - 1 == $len_2 - 1)
                    $per_2 = (int) (($plag_count / ($min_length - 1)) * 100);
                else
                    $per_2 = (int) (($plag_count / ($len_2 - 1)) * 100);
                ?>
                <div class="bar" style="width:<?php echo $per_2; ?>%"><?php echo $per_2; ?>%</div>
                <p>
                    <?php
                    //echo $result1;
                    if ($check == TRUE) {
                        for ($i = 0; $i < $min_length - 1; $i++) {
                            if ($arr[$i] == 1) {
                                echo $arr2[$i];
                                echo " ";
                            } elseif ($arr[$i] != 1) {
                                //echo $arr[$i];
                                $start_end = array();
                                $start_end = $arr[$i];
                                $start_index = $start_end[2];
                                $end_index = $start_end[3];
                                $arrayOfSingleSentence = explode(' ', $arr2[$i]);
                                for ($j = 0; $j < count($arrayOfSingleSentence); $j++) {
                                    if ($start_index == $j) {
                                        for ($z = $start_index; $z <= $end_index; $z++) {
                                            echo "<span> $arrayOfSingleSentence[$z] </span>";
                                        }
                                        $j = $end_index;
                                    } elseif ($start_index != $j) {
                                        echo $arrayOfSingleSentence[$j];
                                        echo " ";
                                    }
                                }
                            }
                            echo ".";
                            //echo "<br>";
                        }
                        if ($min_length != $len_2) {
                            for ($k = $min_length - 1; $k < $len_2; $k++) {
                                echo $arr2[$k];
                                echo " ";
                            }
                        }
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
    <!--result-->

</body>

</html>