<?php
$result2 = $result1 = "";
$check = FALSE;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_file']) && $_POST['submit_file'] == "submit_file") {
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
            //print_r($arr);
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
    </style>
    <script>
        function showFile() {
            $("#choose_file").removeClass("none");
            $("#choose_file").addClass("showDIV");
            $("#choose_text").removeClass("showDIV");
            $("#choose_text").addClass("none");
        }

        function showText() {
            $("#choose_text").removeClass("none");
            $("#choose_text").addClass("showDIV");
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
            <input type="radio" name='thing' value='valuable' id="bank" onclick="showFile()" />
            <label>By Choosing File</label>
            <input type="radio" name='thing' value='valuable' id="school" onclick="showText()" />
            <label>By Writing Text</label>
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

    <!--result-->
    <div>
        <div class="container">
            <h2>Result:</h2>
            <div class="show_result" id="first_result">
                <p>

                </p>
            </div>
        </div>
    </div>
    <!--result-->

</body>

</html>