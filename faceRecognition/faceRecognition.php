<?php
use CV\Mat;
use CV\Face\FaceRecognizer;
use CV\Face\LBPHFaceRecognizer;
use CV\Face\BaseFaceRecognizer;
use CV\Size;
use CV\Scalar;
use CV\Point;
use CV\CascadeClassifier;
use CV\VideoCapture;
use function CV\{
    normalize, imread, cvtColor, equalizeHist, rectangleByRect, imshow, waitKey, putText, imwrite, resize
};
use const CV\{
    NORM_MINMAX, CV_8UC1, CV_8UC3, IMREAD_GRAYSCALE, COLOR_BGR2GRAY, CV_HAAR_SCALE_IMAGE
};

// 创建和返回一个归一化后的图像矩阵
function norm_0_255(Mat $src)
{
    $dst = null;
    switch ($src->channels()) {
        case 1:
            normalize($src, $dst, 0, 255, NORM_MINMAX, CV_8UC1);
            break;
        case 3:
            normalize($src, $dst, 0, 255, NORM_MINMAX, CV_8UC3);
            break;
        default:
            $src->copyTo($dst);
            break;
    }
    return $dst;
}

function read_csv($filename, $separator = ';')
{
    $images = [];
    $labels = [];
    $file = fopen($filename, "r");
    while (!feof($file)) {
        $str = fgets($file);//fgets()函数从文件指针中读取一行
        if ($str) {
            $array = explode($separator, $str);
            $images[] = imread($array[0], IMREAD_GRAYSCALE);
            $labels[] = intval($array[1]);
        }
    }
    fclose($file);
    return [$images, $labels];
}

function run()
{
    $cvsPath = 'at.txt';
    list($images, $labels) = read_csv($cvsPath);
    if (count($images) < 2) {
        die('至少需要两张图片');
    }
    $faceRecognizer = LBPHFaceRecognizer::create();
    $faceRecognizer->train($images, $labels);//识别器训练
    $cascadeClassifier = new CascadeClassifier();
    $cascadeClassifier->load('./haarcascade_frontalface_alt2.xml');//加载人脸识别分类器
    $videoCapture = new VideoCapture(0);//打开默认摄像头
    if (!$videoCapture->isOpened()) {
        die('摄像头开启失败。');
    }

    $isStop = false;
    while (!$isStop) {
        $frame = null;
        $videoCapture->read($frame);//读取图像
        $gray = cvtColor($frame, COLOR_BGR2GRAY);//转为灰度图
        equalizeHist($gray, $gray);
        $faces = null;
        $cascadeClassifier->detectMultiScale($gray, $faces, 1.1, 2, CV_HAAR_SCALE_IMAGE, new Size(50, 50));
        $face = null;
        $textLb = null;
        for ($i = 0; $i < count($faces); $i++) {
            if ($faces[$i]->height > 0 && $faces[$i]->width > 0) {
                $face = $gray->getImageROI($faces[$i]);
                $textLb = new Point($faces[$i]->x, $faces[$i]->y - 10);
                rectangleByRect($frame, $faces[$i], new Scalar(255, 0, 0), 1, 8, 0);
                $faceLabel = $faceRecognizer->predict($face);
                if ($faceLabel == 41) {
                    $name = "hiho";
                    putText($frame, $name, $textLb, 3, 1, new Scalar(0, 0, 255));
                }

            }
        }
        imshow('frame', $frame);
        if (waitKey(50) >= 0) {
            $isStop = true;
        }
    }
}


run();
