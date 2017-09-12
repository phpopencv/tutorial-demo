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
    erode, imshow, dilate
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

/**
 * 调整腐蚀核类型闭包
 * @param $num
 */
$erosionElemClosure = function ($num) {
    global $src;
    global $erosion_elem;
    global $erosion_size;
    $erosion_elem = $num;
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
 * 调整腐蚀核大小闭包
 * @param $num
 */
$erosionSizeClosure = function ($num) {
    global $src;
    global $erosion_elem;
    global $erosion_size;
    $erosion_size = $num;
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
 * 调整膨胀核类型闭包
 * @param $num
 */
$dilationElemClosure = function ($num) {
    global $src;
    global $dilation_elem;
    global $dilation_size;
    $dilation_elem = $num;
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
    imshow("Dilation Demo", $dilation_dst);
};

/**
 * 调整膨胀核大小闭包
 * @param $num
 */
$dilationSizeClosure = function ($num) {
    global $src;
    global $dilation_elem;
    global $dilation_size;
    $dilation_size = $num;
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
    imshow("Dilation Demo", $dilation_dst);
};

function run()
{
    global $src;
    global $erosion_elem;
    global $erosion_size;
    global $dilation_size;
    global $dilation_elem;
    global $erosionElemClosure;
    global $erosionSizeClosure;
    global $dilationElemClosure;
    global $dilationSizeClosure;
    $src = imread("cat.jpg", IMREAD_COLOR);//读取原图像
    if ($src->empty()) {
        die("can't load image.");
    }
    //创建两个窗口并命名
    namedWindow("Erosion Demo", WINDOW_AUTOSIZE);
    namedWindow("Dilation Demo", WINDOW_AUTOSIZE);
    moveWindow("Dilation Demo", $src->cols, 0);
    //添加调整腐蚀核类型滑条
    createTrackbar("Element:\n 0: Rect \n 1: Cross \n 2: Ellipse", "Erosion Demo",
        $erosion_elem, max_elem,
        $erosionElemClosure);
    //添加调整腐蚀核大小滑条
    createTrackbar("Kernel size:\n 2n +1", "Erosion Demo",
        $erosion_size, max_kernel_size,
        $erosionSizeClosure);
    //添加调整膨胀核类型滑条
    createTrackbar("Element:\n 0: Rect \n 1: Cross \n 2: Ellipse", "Dilation Demo",
        $dilation_elem, max_elem,
        $dilationElemClosure);
    //添加调整膨胀核大小滑条
    createTrackbar("Kernel size:\n 2n +1", "Dilation Demo",
        $dilation_size, max_kernel_size,
        $dilationSizeClosure);
    //展示原图
    imshow("Erosion Demo", $src);
    imshow("Dilation Demo", $src);

    waitKey(0);
}


run();
//var_dump($src);
//var_dump($erosion_elem);