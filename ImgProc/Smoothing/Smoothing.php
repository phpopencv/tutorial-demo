<?php
use CV\{
    Mat, Point, Scalar, Size
};
use function CV\{
    putText, imshow, waitKey, namedWindow, imread,
    blur, GaussianBlur, medianBlur, bilateralFilter
};

use const CV\{
    FONT_HERSHEY_COMPLEX, WINDOW_AUTOSIZE, IMREAD_COLOR
};

const DELAY_CAPTION = 1500;
const DELAY_BLUR = 100;
const MAX_KERNEL_LENGTH = 31;

$windowName = 'Smoothing Demo';
$src = null;
$dst = null;

function displayCaption(string $caption)
{
    global $src, $dst, $windowName;
    $dst = Mat::zerosBySize($src->size(), $src->type());
    putText(
        $dst,
        $caption,
        new Point($src->cols / 4, $src->rows / 2),
        FONT_HERSHEY_COMPLEX,
        1,
        new Scalar(255, 255, 255)
    );

    imshow($windowName, $dst);
    $key = waitKey(DELAY_CAPTION);
    if ($key >= 0) {
        return -1;
    }
    return 0;
}

function displayDst(int $delay)
{
    global $dst, $windowName;
    imshow($windowName, $dst);
    $key = waitKey($delay);
    if ($key >= 0) {
        return -1;
    }
    return 0;
}

function run()
{
    global $windowName, $src, $dst;
    namedWindow($windowName, WINDOW_AUTOSIZE);

    // Load the source image
    $src = imread("lena.jpg", IMREAD_COLOR);

    if (displayCaption("Original Image") != 0) {
        return 0;
    }
    $dst = $src->clone();
    if (displayDst(DELAY_CAPTION) != 0) {
        return 0;
    }

    /// Applying Homogeneous blur
    if (displayCaption("Homogeneous Blur") != 0) {
        return 0;
    }

    //![blur]
    for ($i = 1; $i < MAX_KERNEL_LENGTH; $i = $i + 2) {
        blur($src, $dst, new Size($i, $i), new Point(-1, -1));
        if (displayDst(DELAY_BLUR) != 0) {
            return 0;
        }
    }
    //![blur]

    /// Applying Gaussian blur
    if (displayCaption("Gaussian Blur") != 0) {
        return 0;
    }

    //![gaussianblur]
    for ($i = 1; $i < MAX_KERNEL_LENGTH; $i = $i + 2) {
        GaussianBlur($src, $dst, new Size($i, $i), 0, 0);
        if (displayDst(DELAY_BLUR) != 0) {
            return 0;
        }
    }
    //![gaussianblur]

    /// Applying Median blur
    if (displayCaption("Median Blur") != 0) {
        return 0;
    }

    //![medianblur]
    for ($i = 1; $i < MAX_KERNEL_LENGTH; $i = $i + 2) {
        medianBlur($src, $dst, $i);
        if (displayDst(DELAY_BLUR) != 0) {
            return 0;
        }
    }
    //![medianblur]

    /// Applying Bilateral Filter
    if (displayCaption("Bilateral Blur") != 0) {
        return 0;
    }

    //![bilateralfilter]
    for ($i = 1; $i < MAX_KERNEL_LENGTH; $i = $i + 2) {
        bilateralFilter($src, $dst, $i, $i * 2, $i / 2);
        if (displayDst(DELAY_BLUR) != 0) {
            return 0;
        }
    }
    //![bilateralfilter]

    /// Wait until user press a key
    displayCaption("End: Press a key!");

    waitKey(0);

    return 0;


}

run();