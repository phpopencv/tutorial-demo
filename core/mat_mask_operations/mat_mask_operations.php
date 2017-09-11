<?php
use CV\Mat;
use CV\Formatter;
use CV\Scalar;
use const CV\{
    CV_8U,
    CV_8UC3,
    CV_8UC1,
    CV_8UC2
};
use function CV\{
    LUT, imshow, waitKey, getTickCount, getTickFrequency, filter2D
};
use function CV\imread;

function Sharpen(Mat $myImage, &$result)
{
    if ($myImage->depth() != CV_8U) {// accept only uchar images
        throw new Exception("图像必须未unchar类型");
    }

    $nChannels = $myImage->channels();
    $result = Mat::zerosBySize($myImage->size(), $myImage->type());
    for ($j = 1; $j < $myImage->rows - 1; ++$j) {//从矩阵第二行开始循环到倒数第二行
        for ($i = 1; $i < $myImage->cols - 1; ++$i) {//从矩阵第二列开始循环到倒数第二列
            for ($channel = 0; $channel < $nChannels; $channel++) {//循环每个像素的矩阵通道
                $value = $myImage->at($j, $i, $channel) * 5//原中心值乘以5
                    - $myImage->at($j, $i - 1, $channel)//减去中心值左边的值
                    - $myImage->at($j, $i + 1, $channel)//减去中心值右边的值
                    - $myImage->at($j - 1, $i, $channel)//减去中心值上面的值
                    - $myImage->at($j + 1, $i, $channel);//减去中心值下面的值
                $result->at($j, $i, $channel, $value);
            }
        }
    }
    //在图像的边界上，我们未对其做任何掩码操作，所以都赋值为0
    $result->row(0)->setTo(new Scalar(0));
    $result->row($result->rows - 1)->setTo(new Scalar(0));
    $result->col(0)->setTo(new Scalar(0));
    $result->col($result->cols - 1)->setTo(new Scalar(0));
}

$functionResult = null;
$filter2DResult = null;
$myImage = imread('fruits.jpg');
$kernel = new Mat(3, 3, CV\CV_8SC1);
$kernel->at(0, 0, 0, 0);
$kernel->at(0, 1, 0, -1);
$kernel->at(0, 2, 0, 0);
$kernel->at(1, 0, 0, -1);
$kernel->at(1, 1, 0, 5);
$kernel->at(1, 2, 0, -1);
$kernel->at(2, 0, 0, 0);
$kernel->at(2, 1, 0, -1);
$kernel->at(2, 2, 0, 0);
//$kernel->print();
$t = getTickCount();
Sharpen($myImage, $functionResult);
$t = (getTickCount() - $t) / getTickFrequency();
print "Hand written function times passed in seconds:".$t."\r\n";
$t = getTickCount();
filter2D($myImage, $filter2DResult, $myImage->depth(), $kernel);
$t = (getTickCount() - $t) / getTickFrequency();
print "Built-in filter2D times passed in seconds:".$t;
imshow('src', $myImage);
imshow('function result', $functionResult);
imshow('filter2D result', $filter2DResult);
waitKey(0);