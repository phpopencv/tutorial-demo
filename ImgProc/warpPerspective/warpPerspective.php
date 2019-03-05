<?php

use function CV\{
    getPerspectiveTransform, imread, warpPerspective
};
use CV\{
    Point, Size
};

$srcPoints = [
    new Point(165, 270),
    new Point(835, 270),
    new Point(360, 125),
    new Point(615, 125),
];
$dstPoints = [
    new Point(165, 270),
    new Point(835, 270),
    new Point(165, 200),
    new Point(835, 200),
];
$mat = getPerspectiveTransform($srcPoints, $dstPoints, CV\DECOMP_LU);

$image = imread("1.png");
$perspective = null;
warpPerspective($image, $perspective, $mat, new Size(960, 270), 1);
\CV\imshow('test', $perspective);
\CV\waitKey(0);
