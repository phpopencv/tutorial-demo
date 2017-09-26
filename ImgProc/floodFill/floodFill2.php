<?php
use CV\Point;
use CV\Scalar;
use CV\Mat;
use function CV\{
    getStructuringElement, morphologyEx, imshow, namedWindow,
    createTrackbar, getTrackBarPos, waitKey, floodFill, cvtColor, setMouseCallback,
    threshold, destroyWindow
};
use function CV\imread;
use const CV\{
    IMREAD_COLOR, WINDOW_AUTOSIZE, COLOR_BGR2GRAY, CV_8UC1,
    EVENT_LBUTTONDOWN, FLOODFILL_FIXED_RANGE, THRESH_BINARY
};

$g_srcImage = null;//定义原始图
$g_dstImage = null;//目标图
$g_grayImage = null;//灰度图
$g_maskImage = null;//掩模图
$g_nFillMode = 1;//漫水填充的模式
$g_nLowDifference = 20;//负差最大值
$g_nUpDifference = 20;//正差最大值
$g_nConnectivity = 4;//表示floodFill函数标识符低八位的连通值
$g_bIsColor = true;//是否为彩色图的标识符布尔值
$g_bUseMask = false;//是否显示掩膜窗口的布尔值
$g_nNewMaskVal = 255;//新的重新绘制的像素值

function showHelpText()
{
    //输出一些帮助信息
    printf("\n\n\t欢迎来到漫水填充示例程序~");
    printf("\n\n\t本示例根据鼠标选取的点搜索图像中与之颜色相近的点，并用不同颜色标注。");

    printf("\n\n\t按键操作说明: \n\n"
        . "\t\t鼠标点击图中区域- 进行漫水填充操作\n"
        . "\t\t键盘按键【ESC】- 退出程序\n"
        . "\t\t键盘按键【1】-  切换彩色图/灰度图模式\n"
        . "\t\t键盘按键【2】- 显示/隐藏掩膜窗口\n"
        . "\t\t键盘按键【3】- 恢复原始图像\n"
        . "\t\t键盘按键【4】- 使用空范围的漫水填充\n"
        . "\t\t键盘按键【5】- 使用渐变、固定范围的漫水填充\n"
        . "\t\t键盘按键【6】- 使用渐变、浮动范围的漫水填充\n"
        . "\t\t键盘按键【7】- 操作标志符的低八位使用4位的连接模式\n"
        . "\t\t键盘按键【8】- 操作标志符的低八位使用8位的连接模式\n\n");
}

$onMouse = function (int $event, int $x, int $y) {
    global $g_nFillMode;
    global $g_nLowDifference;
    global $g_nUpDifference;
    global $g_nConnectivity;
    global $g_nNewMaskVal;
    global $g_bIsColor;
    global $g_dstImage;
    global $g_grayImage;
    global $g_bUseMask;
    global $g_maskImage;
    // 若鼠标左键没有按下，便返回
    //此句代码的OpenCV2版为：
    //if( event != CV_EVENT_LBUTTONDOWN )
    //此句代码的OpenCV3版为：
    if ($event != EVENT_LBUTTONDOWN)
        return;

    //-------------------【<1>调用floodFill函数之前的参数准备部分】---------------
    $seed = new Point($x, $y);
    $LowDifference = $g_nFillMode == 0 ? 0 : $g_nLowDifference;//空范围的漫水填充，此值设为0，否则设为全局的g_nLowDifference
    $UpDifference = $g_nFillMode == 0 ? 0 : $g_nUpDifference;//空范围的漫水填充，此值设为0，否则设为全局的g_nUpDifference

    //标识符的0~7位为g_nConnectivity，8~15位为g_nNewMaskVal左移8位的值，16~23位为CV_FLOODFILL_FIXED_RANGE或者0。
    //此句代码的OpenCV2版为：
    //int flags = g_nConnectivity + (g_nNewMaskVal << 8) +(g_nFillMode == 1 ? CV_FLOODFILL_FIXED_RANGE : 0);
    //此句代码的OpenCV3版为：
    $flags = $g_nConnectivity + ($g_nNewMaskVal << 8) + ($g_nFillMode == 1 ? FLOODFILL_FIXED_RANGE : 0);

    //随机生成bgr值
    $b = rand(0, 255);//随机返回一个0~255之间的值
    $g = rand(0, 255);//随机返回一个0~255之间的值
    $r = rand(0, 255);//随机返回一个0~255之间的值
    $rect = null;//定义重绘区域的最小边界矩形区域

    $newVal = $g_bIsColor ? new Scalar($b, $g, $r) : new Scalar($r * 0.299 + $g * 0.587 + $b * 0.114);//在重绘区域像素的新值，若是彩色图模式，取Scalar(b, g, r)；若是灰度图模式，取Scalar(r*0.299 + g*0.587 + b*0.114)

    $dst = $g_bIsColor ? $g_dstImage : $g_grayImage;//目标图的赋值
    $area = null;

    //--------------------【<2>正式调用floodFill函数】-----------------------------
    if ($g_bUseMask) {
        //此句代码的OpenCV2版为：
        //threshold(g_maskImage, g_maskImage, 1, 128, CV_THRESH_BINARY);
        //此句代码的OpenCV3版为：
        threshold($g_maskImage, $g_maskImage, 1, 128, THRESH_BINARY);
        $area = floodFill($dst, $seed, $newVal, $g_maskImage, $rect, new Scalar($LowDifference, $LowDifference, $LowDifference),
            new Scalar($UpDifference, $UpDifference, $UpDifference), $flags);
        imshow("mask", $g_maskImage);
    } else {
        $area = floodFill($dst, $seed, $newVal, null, $rect, new Scalar($LowDifference, $LowDifference, $LowDifference),
            new Scalar($UpDifference, $UpDifference, $UpDifference), $flags);
    }

    imshow("result", $dst);
    print_r($area . " 个像素被重绘\n");
};

function run()
{
    showHelpText();
    global $g_srcImage;
    global $g_dstImage;
    global $g_grayImage;
    global $g_maskImage;
    global $g_nLowDifference;
    global $g_nUpDifference;
    global $onMouse;
    global $g_bIsColor;
    global $g_bUseMask;
    $g_srcImage = imread("floodFill2.jpg", 1);
    if ($g_srcImage->empty()) {
        printf("读取图片错误~！ \n");
        return false;
    }

    $g_srcImage->copyTo($g_dstImage);//拷贝源图到目标图
    $g_grayImage = cvtColor($g_srcImage, COLOR_BGR2GRAY);//转换三通道的image0到灰度图
    $g_maskImage = new Mat($g_srcImage->rows + 2, $g_srcImage->cols + 2, CV_8UC1);//利用image0的尺寸来初始化掩膜mask

    namedWindow("result", WINDOW_AUTOSIZE);
    //创建Trackbar
    createTrackbar("Maximum of negative difference", "result", $g_nLowDifference, 255);

    createTrackbar("Maximum positive difference", "result", $g_nUpDifference, 255);
    setMouseCallback("result", $onMouse);
    //循环轮询按键
    while (true) {
        //先显示效果图
        imshow("result", $g_bIsColor ? $g_dstImage : $g_grayImage);

        //获取键盘按键
        $key = waitKey(0);
        //判断ESC是否按下，若按下便退出
        if (($key & 255) == 27) {
            printf("程序退出...........\n");
            break;
        }
        if ($key == -1) {
            continue;
        }
        $key = chr($key);
        //根据按键的不同，进行各种操作
        switch ($key) {
            //如果键盘“1”被按下，效果图在在灰度图，彩色图之间互换
            case '1':
                if ($g_bIsColor)//若原来为彩色，转为灰度图，并且将掩膜mask所有元素设置为0
                {
                    printf("键盘“1”被按下，切换彩色/灰度模式，当前操作为将【彩色模式】切换为【灰度模式】\n");
                    $g_grayImage = cvtColor($g_srcImage, COLOR_BGR2GRAY);
                    $g_maskImage->setTo(new Scalar(0, 0, 0, 0));    //将mask所有元素设置为0
                    $g_bIsColor = false;    //将标识符置为false，表示当前图像不为彩色，而是灰度
                } else//若原来为灰度图，便将原来的彩图image0再次拷贝给image，并且将掩膜mask所有元素设置为0
                {
                    printf("键盘“1”被按下，切换彩色/灰度模式，当前操作为将【彩色模式】切换为【灰度模式】\n");
                    $g_srcImage->copyTo($g_dstImage);
                    $g_maskImage->setTo(new Scalar(0, 0, 0, 0));
                    $g_bIsColor = true;//将标识符置为true，表示当前图像模式为彩色
                }
                break;
            //如果键盘按键“2”被按下，显示/隐藏掩膜窗口
            case '2':
                if ($g_bUseMask) {
                    destroyWindow("mask");
                    $g_bUseMask = false;
                } else {
                    namedWindow("mask", 0);
                    $g_maskImage->setTo(new Scalar(0, 0, 0, 0));
                    imshow("mask", $g_maskImage);
                    $g_bUseMask = true;
                }
                break;
            //如果键盘按键“3”被按下，恢复原始图像
            case '3':
                printf("按键“3”被按下，恢复原始图像\n");
                $g_srcImage->copyTo($g_dstImage);
                $g_grayImage = cvtColor($g_dstImage, COLOR_BGR2GRAY);
                $g_maskImage->setTo(new Scalar(0, 0, 0, 0));
                break;
            //如果键盘按键“4”被按下，使用空范围的漫水填充
            case '4':
                printf("按键“4”被按下，使用空范围的漫水填充\n");
                $g_nFillMode = 0;
                break;
            //如果键盘按键“5”被按下，使用渐变、固定范围的漫水填充
            case '5':
                printf("按键“5”被按下，使用渐变、固定范围的漫水填充\n");
                $g_nFillMode = 1;
                break;
            //如果键盘按键“6”被按下，使用渐变、浮动范围的漫水填充
            case '6':
                printf("按键“6”被按下，使用渐变、浮动范围的漫水填充\n");
                $g_nFillMode = 2;
                break;
            //如果键盘按键“7”被按下，操作标志符的低八位使用4位的连接模式
            case '7':
                printf("按键“7”被按下，操作标志符的低八位使用4位的连接模式\n");
                $g_nConnectivity = 4;
                break;
            //如果键盘按键“8”被按下，操作标志符的低八位使用8位的连接模式
            case '8':
                printf("按键“8”被按下，操作标志符的低八位使用8位的连接模式\n");
                $g_nConnectivity = 8;
                break;
        }
    }
    return true;

}

run();
