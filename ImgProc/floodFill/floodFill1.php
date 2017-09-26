<?php
use CV\Point;
use CV\Scalar;
use CV\Rect;
use function CV\{
    getStructuringElement, morphologyEx, imshow, namedWindow,
    createTrackbar, getTrackBarPos, waitKey, floodFill
};
use function CV\imread;
use const CV\{
    IMREAD_COLOR, WINDOW_AUTOSIZE
};

$src = imread("demo.jpg");
$rect = new Rect(0,0,0,0);
imshow('origin', $src);
floodFill($src, new Point(50, 300), new Scalar(155, 255, 55), null, $rect, new Scalar(20, 20, 20), new Scalar(20, 20, 20));
imshow('result', $src);

waitKey(0);
