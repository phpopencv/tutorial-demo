<?php

use CV\VideoCapture;
use function CV\{
    waitKey, imshow, imwrite,destroyWindow
};

$capture = new VideoCapture();
$capture->open(0);
if (!$capture->isOpened()) {
    exit('打开摄像头失败');
}

$number = 1;
while (true) {

    $frame = null;
    $capture->read($frame);
    imshow("frame", $frame);
    $key = waitKey(50);
    $myPicsPath = realpath('./myPics');
    $filename = $myPicsPath . '/pic' . $number . '.jpg';
    if ($key != -1) {
        $key = chr($key);
    }
    switch ($key) {
        case'p':
            $number++;
            imwrite($filename, $frame);
            imshow("photo", $frame);
            waitKey(500);
            destroyWindow("photo");
            break;
        case 's':
            break 2;
        default:
            break;
    }
}