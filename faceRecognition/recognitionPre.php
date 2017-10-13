<?php
use CV\CascadeClassifier;
use CV\Size;
use CV\Mat;
use function CV\{
    imread, cvtColor, equalizeHist, waitKey,resize,imwrite,imshow
};
use const CV\{
    COLOR_BGR2GRAY,CV_HAAR_DO_ROUGH_SEARCH
};

$cascadeClassifier = new CascadeClassifier();
$cascadeClassifier->load('./haarcascade_frontalface_alt2.xml');

$faces = [];
$imgGray = null;
$number = 10;
for ($i = 1; $i <= $number; $i++) {
    $filePath = './myPics/pic' . $i . '.jpg';
    if ($filePath = realpath($filePath)) {
        $img = imread($filePath);
        $imgGray = cvtColor($img, COLOR_BGR2GRAY);
        equalizeHist($imgGray, $imgGray);
        $cascadeClassifier->detectMultiScale($imgGray, $faces, 1.1, 3, CV_HAAR_DO_ROUGH_SEARCH, new Size(50, 50));
        for ($j = 0; $j < count($faces); $j++) {
            $faceROI = $img->getImageROI($faces[$j]);
            $MyFace = null;
            if ($faceROI->cols > 100) {
                resize($faceROI, $MyFace, new Size(92, 112));
                $facePath = './myFaces/';
                $facePath = realpath($facePath).'/MyFcae' . $i . '.jpg';
                imwrite($facePath, $MyFace);
                imshow("ii", $MyFace);
            }
            waitKey(10);
        }
    }
}