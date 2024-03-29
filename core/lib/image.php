<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class ms_image {

    var $watermark_postion  = 0;        //水印位置，默认为右下角，1：左上角，2：右上角，3：左下角，4右下角，5，剧中
    var $watermark_transparence = 50;   //透明度 :未实现
    var $thumb_mod          = 0;        //缩略图方式，默认为等比例缩放，1表示按比例裁剪

    var $watermark_text = 'Modoer点评系统';
    function __construct() { //PHP 5
    }

    function ms_image() { //PHP 4
        global $_G;
        $this->__construct($name, $exts);
        $this->watermark_text = $_G['cfg']['sitename'];
    }

    //图片加水印
    function watermark($srcimg, $destimg, $waterimg, $level = 80) {
        $simg = $this->_imagecreatefromimg($srcimg);
        if(!$simg) return;
        $sw = imagesx($simg); //目标图片宽
        $sh = imagesy($simg); //目标图片高
        imagealphablending($simg, true); //设定混合模式
        $wimg = $this->_imagecreatefromimg($waterimg); //读取水印文件
        if(!$wimg) return;
        $ww = imagesx($wimg); //水印图片宽
        $wh = imagesy($wimg); //水印图片高

        $postion = $this->watermark_postion ? $this->watermark_postion : mt_rand(1,6);
        if($postion==1) {
            $sx = 5; //右上
            $sy = 10;
        } elseif($postion==2) {
            $sx = $sw - $ww - 5; //左下
            $sy = 10;
        } elseif($postion==3) {
            $sx = 5; //右下
            $sy = $sh - $wh - 10;
        } elseif($postion== 4) {
            $sx = $sw - $ww - 5; //右下角
            $sy = $sh - $wh - 10;
        } elseif($postion==5) {
            $sx = round($sw/2 - $ww/2); //剧中
            $sy = round($sh/2 - $wh/2);
        } elseif($postion==6) { //底部文字
            $imgcreate_fun = function_exists('ImageCreateTrueColor') ? 'ImageCreateTrueColor' : 'ImageCreate';
            $new_img = $imgcreate_fun ($sw , $sh + 50);
            imagecopyresized($new_img ,$simg, 0 ,0 ,0 ,0 ,$sw, $sh+50, $sw , $sh);
            $path_parts = pathinfo($source_img_file);
            $ext_name = strtolower($path_parts['extension']);
            $fun = $this->_imagecreatefun($ext_name);
            if($fun=='imagejpeg') {
                $fun( $new_img, $dest_img_file, $level);
            } else {
                if($fun=='imagegif') {
                    $bgcolor = ImageColorAllocate($new_img ,0, 0, 0);
                    $bgcolor = ImageColorTransparent($new_img, $bgcolor);
                }
                $result = $fun($new_img, $dest_img_file);
            }
            ImageDestroy($simg);
            imagedestroy($new_img);
            return $result;
        }

        imagecopy($simg, $wimg, $sx, $sy, 0, 0, $ww, $wh);
        $result = ImageJPEG($simg, $destimg, $level); //生成
        ImageDestroy($simg);
        ImageDestroy($wimg);
        return $result;
    }

    //截取缩略图
    function thumb($source_img_file, $dest_img_file, $new_width, $new_height, $level = 80) {
        $fun = $this->thumb_mod ? '_thumb2' : '_thumb';
        return $this->$fun($source_img_file, $dest_img_file, $new_width, $new_height, $level);
    }

    //按比例裁剪
    function _thumb($source_img_file, $dest_img_file, $new_width, $new_height, $level = 80) {
        $path_parts = pathinfo($source_img_file);
        $ext_name = strtolower($path_parts['extension']);
        $img = null;
        $img = $this->_imagecreatefromimg($source_img_file);
        $src_x = $src_y = 0;
        if ($img) {
            $imgcreate_fun = function_exists('ImageCreateTrueColor') ? 'ImageCreateTrueColor' : 'ImageCreate';
            $source_img_width = imagesx ($img);
            $source_img_height = imagesy ($img);
            if($source_img_width < $new_width || $source_img_height < $new_height) {
                $new_img_width = min($new_width,$source_img_width);
                $new_img_height = min($new_height,$source_img_height);
                $src_x = $source_img_width > $new_width ? (int) (($source_img_width - $new_width) / 2) : 0;
                $src_y = $source_img_height > $new_height ? (int) (($source_img_height - $new_height) / 2) : 0;
                $new_img = $imgcreate_fun ($new_img_width , $new_img_height);
                imagecopyresized($new_img ,$img, 0 ,0 ,$src_x ,$src_y ,$new_img_width, $new_img_height, $new_img_width , $new_img_height);
            } else {
                $w = $source_img_width / $new_width;
                if($source_img_height/$w >= $new_height) {
                    $new_img_width = $new_width;
                    $new_img_height = (int)($source_img_height / $source_img_width * $new_width);
                } else {
                    $new_img_height = $new_height;
                    $new_img_width = (int)($source_img_width / $source_img_height * $new_height);
                }
                $new_img = $imgcreate_fun ($new_width , $new_height);
                $new_img2 = $imgcreate_fun ($new_img_width , $new_img_height);
                ImageCopyResampled($new_img2, $img, 0, 0, 0, 0, $new_img_width, $new_img_height, $source_img_width, $source_img_height);
                $src_x = $src_y = 0;
                $src_x = $new_img_width > $new_width ? (int) (($new_img_width - $new_width) / 2) : 0;
                $src_y = $new_img_height > $new_height ? (int) (($new_img_height - $new_height) / 2) : 0;
                imagecopyresized($new_img ,$new_img2, 0 ,0 ,$src_x ,$src_y , $new_width, $new_height , $new_width , $new_height);
                imagedestroy($new_img2);
            }
            $fun = $this->_imagecreatefun($ext_name);
            if($fun=='imagejpeg') {
                $fun( $new_img, $dest_img_file, $level);
            } else {
                if($fun=='imagegif') {
                    $bgcolor = ImageColorAllocate($new_img ,0, 0, 0);
                    $bgcolor = ImageColorTransparent($new_img, $bgcolor);
                }
                $fun( $new_img, $dest_img_file);
            }
            imagedestroy($img);
            imagedestroy($new_img);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
    创建缩略图 ，等比例缩放
    按比例，优先使用 new_width 生成，
    new_height 在 $source_img_width > $source_img_height 的情况下 使用
    */
    function _thumb2($source_img_file, $dest_img_file, $new_width, $new_height, $level = 80) {
        $path_parts = pathinfo($source_img_file);
        $ext_name = strtolower($path_parts['extension']);
        $img = null;
        $img = $this->_imagecreatefromimg($source_img_file);
        if ($img) {
            $source_img_width = imagesx ($img);
            $source_img_height = imagesy ($img);
            if ($source_img_width > $source_img_height) {
                $new_img_width = $new_width;
                $new_img_height = (int)($source_img_height / $source_img_width * $new_width);
            } else {
                $new_img_height = $new_height;
                $new_img_width = (int)($source_img_width / $source_img_height * $new_height);
            }
            $imgcreate_fun = 'ImageCreate'; //function_exists('ImageCreateTrueColor') ? 'ImageCreateTrueColor' : 'ImageCreate';
            $new_img = $imgcreate_fun($new_img_width, $new_img_height);
            ImageCopyResampled($new_img, $img, 0, 0, 0, 0, $new_img_width, $new_img_height, $source_img_width, $source_img_height);
            $fun = $this->_imagecreatefun($ext_name);
            if($fun=='imagejpeg') {
                $fun( $new_img, $dest_img_file, $level);
            } else {
                if($fun=='imagegif') {
                    $bgcolor = ImageColorAllocate($new_img ,0, 0, 0);
                    $bgcolor = ImageColorTransparent($new_img, $bgcolor);
                }
                $fun( $new_img, $dest_img_file);
            }
            imagedestroy($img);
            imagedestroy($new_img);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //返回有效的img资源句柄
    function _imagecreatefromimg($img) {
        $ext_name = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        switch($ext_name) {
            case 'gif':
                return function_exists('imagecreatefromgif') ? imagecreatefromgif($img) : false;
                break;
            case 'jpg':
            case 'jpe':
            case 'jpeg':
                return function_exists('imagecreatefromjpeg') ? imagecreatefromjpeg($img) : false;
                break;
            case 'png':
                return function_exists('imagecreatefrompng') ? imagecreatefrompng($img) : false;
                break;
            default:
                return FALSE;
        }
    }

    //返回创建图片的函数
    function _imagecreatefun($ext) {
        switch($ext) {
        case 'gif':
            return 'imagegif';
        case 'png':
            return 'imagepng';
        default:
            return 'imagejpeg';
        }
    }

}

