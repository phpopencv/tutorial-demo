<?php
use CV\Mat;
use CV\Size;
use CV\Point;
use function CV\{
    imread,
    namedWindow,
    moveWindow,
    waitKey,
    createTrackbar,
    getStructuringElement,
    erode, imshow, dilate,
    getTrackBarPos
};
use const CV\{
    IMREAD_COLOR, WINDOW_AUTOSIZE, MORPH_RECT, MORPH_CROSS, MORPH_ELLIPSE
};

$src = null;
$erosion_dst = null;
$dilation_dst = null;
$erosion_elem = 0;
$erosion_size = 0;
$dilation_elem = 0;
$dilation_size = 0;
const max_elem = 2;
const max_kernel_size = 21;

const EROSION_WINDOW_NAME = "Erosion Demo";
const DILATION_WINDOW_NAME = "Dilation Demo";
const ELEM_TYPE_TRACK_BAR_NAME = "Element:\n 0: Rect \n 1: Cross \n 2: Ellipse";
const ELEM_SIZE_TRACK_BAR_NAME = "Kernel size:\n 2n +1";

/**
 * 调整腐蚀(核类型/核大小)闭包
 * @param $num
 */
$erosionClosure = function ($num) {

    global $src;
    global $erosion_elem;
    global $erosion_size;
    $erosion_elem = getTrackBarPos(ELEM_TYPE_TRACK_BAR_NAME, EROSION_WINDOW_NAME);
    $erosion_size = getTrackBarPos(ELEM_SIZE_TRACK_BAR_NAME, EROSION_WINDOW_NAME);
    $erosion_type = 0;
    if ($erosion_elem == 0) {
        $erosion_type = MORPH_RECT;
    } else if ($erosion_elem == 1) {
        $erosion_type = MORPH_CROSS;
    } else if ($erosion_elem == 2) {
        $erosion_type = MORPH_ELLIPSE;
    }
    $element = getStructuringElement($erosion_type,
        new Size(2 * $erosion_size + 1, 2 * $erosion_size + 1),
        new Point($erosion_size, $erosion_size));
    $erosion_dst = null;
    erode($src, $erosion_dst, $element);
    imshow("Erosion Demo", $erosion_dst);
};


/**
 * 调整膨胀(核类型/核大小)闭包
 * @param $num
 */
$dilationClosure = function ($num) {
    global $src;
    global $dilation_elem;
    global $dilation_size;
    $dilation_elem = getTrackBarPos(ELEM_TYPE_TRACK_BAR_NAME, DILATION_WINDOW_NAME);
    $dilation_size = getTrackBarPos(ELEM_SIZE_TRACK_BAR_NAME, DILATION_WINDOW_NAME);
    $dilation_type = 0;
    if ($dilation_elem == 0) {
        $dilation_type = MORPH_RECT;
    } else if ($dilation_elem == 1) {
        $dilation_type = MORPH_CROSS;
    } else if ($dilation_elem == 2) {
        $dilation_type = MORPH_ELLIPSE;
    }
    $element = getStructuringElement($dilation_type,
        new Size(2 * $dilation_size + 1, 2 * $dilation_size + 1),
        new Point($dilation_size, $dilation_size));
    $dilation_dst = null;
    dilate($src, $dilation_dst, $element);
    imshow(DILATION_WINDOW_NAME, $dilation_dst);
};


function run()
{
    global $src;
    global $erosion_elem;
    global $erosion_size;
    global $dilation_size;
    global $dilation_elem;
    global $erosionClosure;
    global $dilationClosure;
    $src = imread("cat.jpg", IMREAD_COLOR);//读取原图像
    if ($src->empty()) {
        die("can't load image.");
    }
    //创建两个窗口并命名
    namedWindow("Erosion Demo", WINDOW_AUTOSIZE);
    namedWindow("Dilation Demo", WINDOW_AUTOSIZE);
    moveWindow("Dilation Demo", $src->cols, 0);
    //添加调整腐蚀核类型滑条
    createTrackbar(ELEM_TYPE_TRACK_BAR_NAME, EROSION_WINDOW_NAME,
        $erosion_elem, max_elem,
        $erosionClosure);
    //添加调整腐蚀核大小滑条
    createTrackbar(ELEM_SIZE_TRACK_BAR_NAME, EROSION_WINDOW_NAME,
        $erosion_size, max_kernel_size,
        $erosionClosure);
    //添加调整膨胀核类型滑条
    createTrackbar(ELEM_TYPE_TRACK_BAR_NAME, DILATION_WINDOW_NAME,
        $dilation_elem, max_elem,
        $dilationClosure);
    //添加调整膨胀核大小滑条
    createTrackbar(ELEM_SIZE_TRACK_BAR_NAME, DILATION_WINDOW_NAME,
        $dilation_size, max_kernel_size,
        $dilationClosure);
    //展示原图
    imshow("Erosion Demo", $src);
    imshow("Dilation Demo", $src);

    waitKey(0);
}


run();
//var_dump($src);
//var_dump($erosion_elem);