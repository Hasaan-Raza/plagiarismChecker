<?php
$result2 = $result1 = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_file']) && $_POST['submit_file'] == "submit_file") {
        $a = $_POST['file_1'];
        $b = $_POST['file_2'];

        $myfile1 = fopen("$a", "r") or die("Unable to open file!");
        $myfile2 = fopen("$b", "r") or die("Unable to open file!");

        $result1 = fread($myfile1, filesize("$a"));
        $result2 = fread($myfile2, filesize("$b"));

        $arr1 = explode('.', $result1);
        $arr2 = explode('.', $result2);

        $len_1 = count($arr1);
        $len_2 = count($arr2);
        $n = min($len_1, $len_2);

        for ($i = 0; $i < $n; $i++) {
            $result = compare_string($arr1[$i], $arr2[$i]);
            print_r($result);
        }

        fclose($myfile1);
        fclose($myfile2);
    } else if (isset($_POST['submit_text']) && $_POST['submit_text'] == "submit_text") {
        $result1 = $_POST['text_1'];
        $result2 = $_POST['text_2'];
        $arr1 = explode('.', $result1);
        $arr2 = explode('.', $result2);

        $len_1 = count($arr1);
        $len_2 = count($arr2);
        $n = min($len_1, $len_2);

        for ($i = 0; $i < $n; $i++) {
            $result = compare_string($arr1[$i], $arr2[$i]);
            print_r($result);
        }
    }
}

function compare_string($s1, $s2)
{
    if ($s1 == "" || $s2 == "")
        return;
    $test1 = explode(' ', $s1);
    $test2 = explode(' ', $s2);
    $size_1 = count($test1);
    $size_2 = count($test2);
    print_r($test1);
    print_r($test2);
    $k = 0;
    $a = 0;
    $b = 0;
    $flag = TRUE;
    $x = 0;
    $y = 0;
    for ($i = $x; $i < $size_1; $i++) {
        for ($j = $y; $j < $size_2; $j++) {
            hello: if ($test2[$j] == $test1[$i] && ($test1[$i] != "" || $test2[$j] != "")) {
                if ($k == 0) {
                    $a = $i;
                    $b = $j;
                }
                $k = $k + 1;
                $i = $i + 1;
                $j = $j + 1;
                if ($k == 3) {
                    $flag = FALSE;
                    break;
                }
                goto hello;
            } else {
                $k = 0;
            }
        }
        if ($flag == FALSE)
            break;
    }
    if ($flag == TRUE)
        return array(0, 0);
    else if ($flag == FALSE)
        return array($a, $b);
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
            padding-top: 50px;
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
                <input type="file" name="file_1" value="File 1">
                <input type="file" name="file_2" value="File 2">
                <input type="submit" name="submit_file" value="submit_file">
            </form>
        </div>
    </div>
    <div id="choose_text" class="none">
        <div class="container">
            <form method="POST" action="">
                <textarea style="resize: none; outline: none;" rows="10" cols="50" name="text_1" placeholder="Enter text here"></textarea>
                <textarea style="margin-left: 50px; resize: none; outline: none;" rows="10" cols="50" name="text_2" placeholder="Enter text here"></textarea><br>
                <input id="btn" type="submit" name="submit_text" value="submit_text">
            </form>
        </div>

    </div>
    <div>
        <div class="container">
            <h2>Result:</h2>
            <textarea style="resize: none; outline: none;" rows="10" cols="50" name="copytext_1" placeholder="Copied text"><?php echo $result1; ?></textarea>
            <textarea style="margin-left: 50px; resize: none; outline: none;" rows="10" cols="50" name="copytext_2" placeholder="Copied text"><?php echo $result2; ?></textarea>
        </div>

    </div>

</body>

</html>