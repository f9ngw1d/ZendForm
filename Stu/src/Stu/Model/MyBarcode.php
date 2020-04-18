<?php
namespace Stu\Model;
/**
 * @author cry
 * @function 生成一维码
 */
class MyBarcode{
    // For demonstration purposes, get pararameters that are passed in through $_GET or set to the default value
    // 出于演示目的，获取通过$_GET传入或设置为默认值的参数

    /* This function call can be copied into your project and can be made from anywhere in your code
       这个函数调用可以复制到您的项目中，并且可以在代码的任何地方执行*/
    //barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor );
    /**
     * @param string $text  用于生成一维码的内容，内容要符合$code_type里面的数组值
     * @param bool $print   是否把$text文字内容显示到一维码 true：是；false：否
     * @param string $filepath
     * @param string $size
     * @param string $orientation
     * @param string $code_type  不同的字符集，调用的方式不同【可选：code128/code128b，128a，code39，code25，codabar】
     * @param int $SizeFactor  图片要放大的乘数 1：默认为原图
     */
    function createBarcode( $text ="0", $filepath="", $print=true , $size="20", $orientation="horizontal", $code_type="code128", $SizeFactor=1 ) {
        $code_string = "";
        /* Translate the $text into barcode the correct $code_type
           将$text转换为正确的$code_type的条形码*/
        if ( in_array(strtolower($code_type), array("code128", "code128b")) ) { //1 - $code_type 为 code128 / code128b
            $chksum = 104;
            /* Must not change order of array elements as the checksum depends on the array's key to validate final code
               必须不可以更改数组元素的顺序，因为校验和取决于数组的键来验证最终代码 */
            $code_array = array( //110
                " "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213",
                "*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222",
                "0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",
                ":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121",
                "A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133",
                "K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311",
                "U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311",
                "["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","\`"=>"111422",
                "a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221","e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211",
                "k"=>"241211","l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241","s"=>"114212","t"=>"124112",
                "u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141","z"=>"214121",
                "{"=>"412121","|"=>"111143","}"=>"111341","~"=>"131141","DEL"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141",
                "FNC 4"=>"114131","CODE A"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112"
            );
            $code_keys = array_keys($code_array);// 返回包含数组中所有键名的一个新数组
            $code_values = array_flip($code_keys);//反转数组中所有的键以及它们关联的值
            for ( $X = 1; $X <= strlen($text); $X++ ) {
                $activeKey = substr( $text, ($X-1), 1); //每次取一位，依次后移
                $code_string .= $code_array[$activeKey];
                $chksum=($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211214" . $code_string . "2331112";   //211214：Start B    2331112：Stop
        }
        elseif ( strtolower($code_type) == "code128a" ) {
            $chksum = 103;
            $text = strtoupper($text); // Code 128A doesn't support lower case
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(
                " "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213",
                "*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222",
                "0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122","
			:"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121",
                "A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133",
                "K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311",
                "U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111",
                "_"=>"111224","NUL"=>"111422","SOH"=>"121124","STX"=>"121421","ETX"=>"141122","EOT"=>"141221","ENQ"=>"112214","ACK"=>"112412","BEL"=>"122114",
                "BS"=>"122411","HT"=>"142112","LF"=>"142211","VT"=>"241211","FF"=>"221114","CR"=>"413111","SO"=>"241112","SI"=>"134111","DLE"=>"111242",
                "DC1"=>"121142","DC2"=>"121241","DC3"=>"114212","DC4"=>"124112","NAK"=>"124211","SYN"=>"411212","ETB"=>"421112","CAN"=>"421211","EM"=>"212141",
                "SUB"=>"214121","ESC"=>"412121","FS"=>"111143","GS"=>"111341","RS"=>"131141","US"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311",
                "CODE C"=>"113141","CODE B"=>"114131","FNC 4"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232",
                "Stop"=>"2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ( $X = 1; $X <= strlen($text); $X++ ) {
                $activeKey = substr( $text, ($X-1), 1);
                $code_string .= $code_array[$activeKey];
                $chksum=($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211412" . $code_string . "2331112";//211214：Start A    2331112：Stop
        }
        elseif ( strtolower($code_type) == "code39" ) {
            $code_array = array(
                "0"=>"111221211","1"=>"211211112","2"=>"112211112","3"=>"212211111","4"=>"111221112",
                "5"=>"211221111","6"=>"112221111","7"=>"111211212","8"=>"211211211","9"=>"112211211",
                "A"=>"211112112","B"=>"112112112","C"=>"212112111","D"=>"111122112","E"=>"211122111",
                "F"=>"112122111","G"=>"111112212","H"=>"211112211","I"=>"112112211","J"=>"111122211",
                "K"=>"211111122","L"=>"112111122","M"=>"212111121","N"=>"111121122","O"=>"211121121",
                "P"=>"112121121","Q"=>"111111222","R"=>"211111221","S"=>"112111221","T"=>"111121221",
                "U"=>"221111112","V"=>"122111112","W"=>"222111111","X"=>"121121112","Y"=>"221121111",
                "Z"=>"122121111","-"=>"121111212","."=>"221111211"," "=>"122111211","$"=>"121212111",
                "/"=>"121211121","+"=>"121112121","%"=>"111212121","*"=>"121121211");

            // Convert to uppercase
            $upper_text = strtoupper($text);  //转换为大写，故code39

            for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
                $code_string .= $code_array[substr( $upper_text, ($X-1), 1)] . "1";
            }

            $code_string = "1211212111" . $code_string . "121121211";//"*"=>"121121211"
        }
        elseif ( strtolower($code_type) == "code25" ) {
            $code_array1 = array("1","2","3","4","5","6","7","8","9","0");
            $code_array2 = array("3-1-1-1-3","1-3-1-1-3","3-3-1-1-1","1-1-3-1-3","3-1-3-1-1","1-3-3-1-1","1-1-1-3-3","3-1-1-3-1","1-3-1-3-1","1-1-3-3-1");

            for ( $X = 1; $X <= strlen($text); $X++ ) {
                for ( $Y = 0; $Y < count($code_array1); $Y++ ) {
                    if ( substr($text, ($X-1), 1) == $code_array1[$Y] )
                        $temp[$X] = $code_array2[$Y];
                }
            }

            for ( $X=1; $X<=strlen($text); $X+=2 ) {
                if ( isset($temp[$X]) && isset($temp[($X + 1)]) ) {
                    $temp1 = explode( "-", $temp[$X] );
                    $temp2 = explode( "-", $temp[($X + 1)] );
                    for ( $Y = 0; $Y < count($temp1); $Y++ )
                        $code_string .= $temp1[$Y] . $temp2[$Y]; //两个数字对应编码交叉出现
                }
            }
            $code_string = "1111" . $code_string . "311";
        }
        elseif ( strtolower($code_type) == "codabar" ) {
            $code_array1 = array("1","2","3","4","5","6","7","8","9","0","-","$",":","/",".","+","A","B","C","D");
            $code_array2 = array("1111221","1112112","2211111","1121121","2111121","1211112","1211211","1221111","2112111","1111122","1112211","1122111","2111212","2121112","2121211","1121212","1122121","1212112","1112122","1112221");

            // Convert to uppercase
            $upper_text = strtoupper($text);

            for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
                for ( $Y = 0; $Y<count($code_array1); $Y++ ) {
                    if ( substr($upper_text, ($X-1), 1) == $code_array1[$Y] )
                        $code_string .= $code_array2[$Y] . "1";
                }
            }
            $code_string = "11221211" . $code_string . "1122121";
        }

        // Pad the edges of the barcode 把条形码的边缘垫起来
        $code_length = 20;
        if ($print) {
            $text_height = 30;
        } else {
            $text_height = 0;
        }

        for ( $i=1; $i <= strlen($code_string); $i++ ){ //加密后的code_string  按位相加，再加20
            $code_length = $code_length + (integer)(substr($code_string,($i-1),1));
        }

        if ( strtolower($orientation) == "horizontal" ) { //水平 or 垂直
            $img_width = $code_length*$SizeFactor;
            $img_height = $size;
        } else {
            $img_width = $size;
            $img_height = $code_length*$SizeFactor;
        }

        $image = imagecreate($img_width, $img_height + $text_height);//参数 x ，y 分别为要创建图像的宽度和高度像素值，返回一个图像资源
        $black = imagecolorallocate ($image, 0, 0, 0);//为一幅图像分配颜色
        $white = imagecolorallocate ($image, 255, 255, 255);

        imagefill( $image, 0, 0, $white ); //函数用于图像区域填充
        if ( $print ) {
            imagestring($image, 5, 31, $img_height, $text, $black );
            //用 $black 颜色将字符串 s 画到 image 所代表的图像的 x，y 坐标处（这是字符串左上角坐标，整幅图像的左上角为 0，0）。如果 font 是 1，2，3，4 或 5，则使用内置字体。
        }

        $location = 10;
        for ( $position = 1 ; $position <= strlen($code_string); $position++ ) {
            $cur_size = $location + ( substr($code_string, ($position-1), 1) );
            if ( strtolower($orientation) == "horizontal" )
                imagefilledrectangle( $image, $location*$SizeFactor, 0, $cur_size*$SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black) );
            //image 由图像创建函数之一返回的图像资源，例如imagecreatetruecolor（）。x1 点1的x坐标。y1 点1的y坐标。 x2 第2点的x坐标。y2 第2点的y坐标。color 填充颜​​色。使用imagecolorallocate（）创建的颜色标识符。
            else
                imagefilledrectangle( $image, 0, $location*$SizeFactor, $img_width, $cur_size*$SizeFactor, ($position % 2 == 0 ? $white : $black) );
            $location = $cur_size;
        }

        // Draw barcode to the screen or save in a file 将条形码绘制到屏幕上或保存到文件中
        if ( $filepath=="" ) {
            header ('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
        } else {
            imagepng($image,$filepath);//imagepng() 将 GD 图像流（image）以 PNG 格式输出到标准输出（通常为浏览器），或者如果用 filename 给出了文件名则将其输出到该文件。imagegif()/imagejpeg()
            imagedestroy($image);
        }
    }
}