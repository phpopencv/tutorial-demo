<?php
use function CV\{ imread, imshow, addWeighted, waitkey};

$alpha = 0.5;
$beta = 0.0;
$dst = null;

fwrite(STDOUT, "* Enter alpha [0-1]: ");
$input = trim(fgets(STDIN));

if( $input >= 0 && $input <= 1 ) {
    $alpha = $input;
}

$src1 = imread('./LinuxLogo.jpg');//load image
$src2 = imread('./WindowsLogo.jpg');//load image

$beta = 1 - $alpha;
addWeighted($src1, $alpha, $src2, $beta, 0.0, $dst);

imshow( "Linear Blend", $dst );
waitKey(0);
return 0;