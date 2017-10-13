<?php

function searchDir($path, &$data)
{
    //判断是否未文件夹
    if (is_dir($path)) {
        $dp = dir($path);//
        while ($file = $dp->read()) {
            if ($file != '.' && $file != '..') {
                searchDir($path . '/' . $file, $data);
            }
        }
        $dp->close();
    }
    //判断是否未文件
    if (is_file($path)) {
        $data[] = $path;//加到data数组中
    }
}

function getDir($dir)
{
    $data = array();
    searchDir($dir, $data);
    return $data;
}


$paths = getDir('./att_faces');
//var_dump($paths);
$fp = fopen("at.txt", "w");
$pwdPath = realpath('./att_faces');
$strStartLen = strlen($pwdPath . "/s");
sort($paths, SORT_STRING);
$i = 0;
$oldNum = -1;
foreach ($paths as $key => $path) {
    $realpath = realpath($path);
    $info = explode("/", $realpath);
    $num = count($info) - 1;
    $filename = $info[$num];
    if (strpos($filename, ".pgm") || strpos($filename, ".jpg")) {
        $endLen = strpos($realpath, '/' . $filename);
        $num = substr($realpath, $strStartLen, $endLen - $strStartLen);
        $flag = fwrite($fp, $realpath . ';' . $num . "\r\n");
    }
}
fclose($fp);
