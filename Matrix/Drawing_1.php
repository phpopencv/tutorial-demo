<?php
namespace CV;

use CV\{
    Mat, Point, Size, Scalar
};
use const CV\{
    CV_8UC3, LINE_8, FILLED
};
use function CV\{
    ellipse, circle, fillPoly, rectangleByPoint, line, imshow, moveWindow, waitKey
};

define('w', 400);

$atom_window = "Drawing 1: Atom";
$rook_window = "Drawing 2: Rook";
$atom_image = Mat::zeros(w, w, CV_8UC3);
$rook_image = Mat::zeros(w, w, CV_8UC3);
MyEllipse($atom_image, 90);
MyEllipse($atom_image, 0);
MyEllipse($atom_image, 45);
MyEllipse($atom_image, -45);
MyFilledCircle($atom_image, new Point(w / 2, w / 2));
MyPolygon($rook_image);
rectangleByPoint(
    $rook_image,
    new Point(0, 7 * w / 8),
    new Point(w, w),
    new Scalar(0, 255, 255),
    FILLED,
    LINE_8);
MyLine($rook_image, new Point(0, 15 * w / 16), new Point(w, 15 * w / 16));
MyLine($rook_image, new Point(w / 4, 7 * w / 8), new Point(w / 4, w));
MyLine($rook_image, new Point(w / 2, 7 * w / 8), new Point(w / 2, w));
MyLine($rook_image, new Point(3 * w / 4, 7 * w / 8), new Point(3 * w / 4, w));
imshow($atom_window, $atom_image);
moveWindow($atom_window, 0, 200);
imshow($rook_window, $rook_image);
moveWindow($rook_window, w, 200);
waitKey(0);
return (0);

function MyEllipse($img, $angle)
{
    $thickness = 2;
    $lineType = 8;
    ellipse(
        $img,
        new Point(w / 2, w / 2),
        new Size(w / 4, w / 16),
        $angle,
        0,
        360,
        new Scalar(255, 0, 0),
        $thickness,
        $lineType);
}

function MyFilledCircle($img, $center)
{
    circle(
        $img,
        $center,
        w / 32,
        new Scalar(0, 0, 255),
        FILLED,
        LINE_8);
}

function MyPolygon($img)
{
    $lineType = LINE_8;
    $rook_points[0] = new Point(w / 4, 7 * w / 8);
    $rook_points[1] = new Point(3 * w / 4, 7 * w / 8);
    $rook_points[2] = new Point(3 * w / 4, 13 * w / 16);
    $rook_points[3] = new Point(11 * w / 16, 13 * w / 16);
    $rook_points[4] = new Point(19 * w / 32, 3 * w / 8);
    $rook_points[5] = new Point(3 * w / 4, 3 * w / 8);
    $rook_points[6] = new Point(3 * w / 4, w / 8);
    $rook_points[7] = new Point(26 * w / 40, w / 8);
    $rook_points[8] = new Point(26 * w / 40, w / 4);
    $rook_points[9] = new Point(22 * w / 40, w / 4);
    $rook_points[10] = new Point(22 * w / 40, w / 8);
    $rook_points[11] = new Point(18 * w / 40, w / 8);
    $rook_points[12] = new Point(18 * w / 40, w / 4);
    $rook_points[13] = new Point(14 * w / 40, w / 4);
    $rook_points[14] = new Point(14 * w / 40, w / 8);
    $rook_points[15] = new Point(w / 4, w / 8);
    $rook_points[16] = new Point(w / 4, 3 * w / 8);
    $rook_points[17] = new Point(13 * w / 32, 3 * w / 8);
    $rook_points[18] = new Point(5 * w / 16, 13 * w / 16);
    $rook_points[19] = new Point(w / 4, 13 * w / 16);
    fillPoly($img,
        $rook_points,
        new Scalar(255, 255, 255),
        $lineType);
}

function MyLine($img, $start, $end)
{
    $thickness = 2;
    $lineType = LINE_8;
    line(
        $img,
        $start,
        $end,
        new Scalar(0, 0, 0),
        $thickness,
        $lineType);
}