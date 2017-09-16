<?php

use CV\Size;
use CV\Point;
use function CV\{
    getStructuringElement, morphologyEx, imshow, namedWindow,
    createTrackbar, getTrackBarPos, waitKey
};
use function CV\imread;
use const CV\{
    IMREAD_COLOR, WINDOW_AUTOSIZE
};

$src = null;
$dst = null;

$morph_elem = 0;
$morph_size = 0;
$morph_operator = 0;
const max_operator = 4;
const max_elem = 2;
const max_kernel_size = 21;

const window_name = "Morphology Transformations Demo";
const morphOperatorTrackBarName = "Operator:0: Opening - 1: Closing  \n 2: Gradient - 3: Top Hat \n 4: Black Hat";
const morphElemTrackBarName = "Element:\n 0: Rect - 1: Cross - 2: Ellipse";
const morphSizeTrackBarName = "Kernel size:\n 2n +1";

/**
 * 定义滑条回调闭包
 */
$Morphology_Operations = function () {
    global $morph_operator;
    global $morph_elem;
    global $morph_size;
    global $src;
    global $dst;

    $morph_operator = getTrackBarPos(morphOperatorTrackBarName, window_name);
    $morph_elem = getTrackBarPos(morphElemTrackBarName, window_name);
    $morph_size = getTrackBarPos(morphSizeTrackBarName, window_name);
    // Since MORPH_X : 2,3,4,5 and 6
    //![operation]
    $operation = $morph_operator + 2;
    //![operation]

    $element = getStructuringElement($morph_elem, new Size(2 * $morph_size + 1, 2 * $morph_size + 1), new Point($morph_size, $morph_size));
    /// Apply the specified morphology operation
    morphologyEx($src, $dst, $operation, $element);
    imshow(window_name, $dst);
};


function run()
{
    global $src;
    global $Morphology_Operations;
    global $morph_operator;
    global $morph_elem;
    global $morph_size;
    $src = imread('baboon.jpg', IMREAD_COLOR); // Load an image

    if ($src->empty()) {
        return false;
    }

    namedWindow(window_name, WINDOW_AUTOSIZE); // Create window

    //创建形态学操作类型调整滑条
    createTrackbar(morphOperatorTrackBarName, window_name,
        $morph_operator, max_operator,
        $Morphology_Operations);
    //创建操作的核类型调整滑条
    createTrackbar(morphElemTrackBarName, window_name,
        $morph_elem, max_elem,
        $Morphology_Operations);
    //创建核大小调整滑条
    createTrackbar(morphSizeTrackBarName, window_name,
        $morph_size, max_kernel_size,
        $Morphology_Operations);
    imshow(window_name, $src);

    waitKey(0);
    return true;
}

run();