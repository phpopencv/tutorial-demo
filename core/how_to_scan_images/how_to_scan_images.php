<?php
use CV\Mat;
use CV\Formatter;
use const CV\{
    CV_8U,
    CV_8UC3,
    CV_8UC1,
    CV_8UC2
};
use function CV\{
    imread, LUT, imshow, waitKey, getTickCount, getTickFrequency
};

$divideWith = 50;
$table = [];
for ($i = 0; $i < 256; ++$i) {
    $table[$i] = $divideWith * intval($i / $divideWith);
}
$lut = new Mat(1, 256, CV_8U);
//$lut->print(Formatter::FMT_PYTHON);

for ($i = 0; $i < 256; $i++) {
    $lut->at(0, $i, 0, $table[$i]);
}
//$lut->print(Formatter::FMT_PYTHON);

$src = imread("./1.jpg");
$dst = null;
$timeStart = getTickCount();
//查找表操作
LUT($src, $lut, $dst);
$time = (getTickCount() - $timeStart) / getTickFrequency();
echo 'LUT方法时间为：'.$time."\r\n";

imshow("Original image", $src);
imshow("Result image", $dst);

waitKey(0);