<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->lib('upload_file', NULL, FALSE);
class ms_upload_image extends ms_upload_file {

    var $userWatermark      = false;    //图片水印
    var $watermark_postion  = 0;        //水印位置，默认为右下角，1：左上角，2：右上角，3：左下角，4右下角，5，剧中
    var $watermark_transparence = 50;   //透明度 :未实现
    var $watermark_text     = 'www.modoer.com'; //文字水印
    var $mment              = null;    //上传图片最大尺寸
    var $thumb_mod          = 0;        //缩略图方式，默认为等比例缩放，1表示按比例裁剪
    var $thumbs             = array();  //需要缩略图的规格列表

    // only read
    var $thumb_filenames    = array();
    var $width              = 0;
    var $height             = 0;
    var $type               = 0;
    var $attr               = '';

    var $thumb_level        = 80;

    function __construct($name, $exts = '') { //PHP 5
        if(!$exts) $exts = 'jpg jpeg png gif';
        parent::__construct($name, $exts);
        $wt = _G('cfg','watermark_text');
        $wt && $this->set_watermark_text($wt);
        $mment_width = (int) _G('cfg','picture_max_width');
        !$mment_width && $mment_width = 800;
        $mment_height = (int) _G('cfg','picture_max_height');
        !$mment_height && $mment_height = 600;
        $this->set_picture_max_size($mment_width,$mment_height);
    }

    //设置文字水印
    function set_watermark_text($text) {
        if(!$text) return;
        $this->watermark_text = $text;
        $this->convert_text();
    }

    //设置图片最大尺寸
    function set_picture_max_size($width,$height) {
        $this->mment['width'] = $width;
        $this->mment['height'] = $height;
    }

    function ms_upload_image($name, $exts = '') { //PHP 4
        $this->__construct($name, $exts);
    }
    
    function add_thumb($keyname, $prefix, $width, $height, $level = 0) {
        $this->thumbs[$keyname] = array(
            'prefix' => $prefix, 
            'width' => $width, 
            'height' => $height, 
            'level' => $level>0 ? $level : $this->thumb_level,
        );
    }

    function set_thumb_level($level) {
        if($level > 0 && $level <= 100) {
            $this->thumb_level = $level;
        }
    }

    function upload($folder, $subdir = 'MONTH', $mment = NULL, $delete_sorcue = FALSE) {

        parent::upload($folder, $subdir);

        $sorcuefile = MUDDER_ROOT . $this->path . DS . $this->filename;
        if(function_exists('getimagesize') && !@getimagesize($sorcuefile)) {
            $this->delete_file();
            redirect('global_upload_image_unkown');
        }

        $thumbfunc = $this->thumb_mod ? 'create_thumb2' : 'create_thumb';

        list($this->width, $this->height, $this->type, $this->attr) = @getimagesize($sorcuefile);
        
        /*
        $thumb = array ( 
            array (
                'prefix' => 's_', 
                'width' => '124', 
                'height' => '94',
                'level' => '80',
            ),
         );
        */
        if($this->thumbs) foreach($this->thumbs as $key => $val) {
            $savefile = MUDDER_ROOT . $this->path . DS . $val['prefix'] . $this->filename;
            $this->$thumbfunc($sorcuefile, $savefile, $val['width'], $val['height'], $val['level']);

            $this->thumb_filenames[$key]['filename'] = $val['prefix'] . $this->filename;
            list($this->thumb_filenames[$key]['width'], $this->thumb_filenames[$key]['height'], $this->thumb_filenames[$key]['type'], $this->thumb_filenames[$key]['attr']) = @getimagesize($savefile);
        }

        //new size
        $mment = $mment ? $mment : $this->mment;
        if($mment && ($this->width>$mment['width']||$this->height>$mment['height'])) {
            $this->$thumbfunc($sorcuefile, $sorcuefile, $mment['width'], $mment['height'], $this->thumb_level);
        }
        //watermark
        if(($this->userWatermark) && is_file($w = MUDDER_ROOT . 'static' . DS . 'images' . DS . 'watermark.png')) {
            $this->watermark($sorcuefile, $sorcuefile, $w, $this->thumb_level);
        }
        //delete
        if($delete_sorcue) {
            $this->delete_file();
        }
        return TRUE;
    }

    //图片加水印
    function watermark($srcimg, $destimg, $waterimg, $level = 80) {
        if($this->is_anim($srcimg)) return; //动画不打水印
        $simg = $this->imagecreatefromimg($srcimg);
        if(!$simg) return;
        $path_parts = pathinfo($srcimg);
        $ext_name = strtolower($path_parts['extension']);
        $sw = imagesx($simg); //目标图片宽
        $sh = imagesy($simg); //目标图片高
        imagealphablending($simg, true); //设定混合模式
        $wimg = $this->imagecreatefromimg($waterimg); //读取水印文件
        if(!$wimg) return;
        $ww = imagesx($wimg); //水印图片宽
        $wh = imagesy($wimg); //水印图片高

        $postion = $this->watermark_postion ? $this->watermark_postion : mt_rand(1,5);
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
            $fontfiles = MUDDER_ROOT . 'static/images/fonts/simsun.ttc';
            if(is_file($fontfiles) && $this->watermark_text) {
                $fontinfo = imagettfbbox(10, 0, $fontfiles, $this->watermark_text);
                $font_width = max($fontinfo[2],$fontinfo[4]);
                if($font_width < $sw) {
                    $new_img = ImageCreateTrueColor ($sw , $sh + 20);
                    imagecopy($new_img, $simg, 0, 0, 0, 0, $sw, $sh);
                    //if($ext_name == 'gif') {
                    //    $color = imagecolorallocate($new_img, 0, 0, 0);
                   // } else {
                        $color = imagecolorallocate($new_img, 255, 255, 255);
                    
                    imagettftext($new_img, 10, 0, 1, $sh + abs($fontinfo[7])+2, $color, $fontfiles, $this->watermark_text);
                    $fun = $this->imagecreatefun($ext_name);
                    if($fun == 'imagejpeg') {
                        $fun( $new_img, $destimg, $level);
                    } else {
                        if($fun == 'imagegif') {
                            //$bgcolor = ImageColorAllocate($new_img ,0, 0, 0);
                            //$bgcolor = ImageColorTransparent($new_img, $bgcolor);
                        }
                        $result = $fun($new_img, $destimg);
                    }
                    ImageDestroy($simg);
                    ImageDestroy($new_img);
                    return $result;
                } else {
                    $mark_invalid = true;
                }
            } else {
                $mark_invalid = true;
            }
        }

        if(!$mark_invalid) {
            imagecopy($simg, $wimg, $sx, $sy, 0, 0, $ww, $wh);
        }
        $fun = $this->imagecreatefun($ext_name);
		if($fun=='imagejpeg') {
			$fun( $simg, $destimg, $level);
		} else {
			if($fun=='imagegif') {
				$bgcolor = ImageColorAllocate($new_img ,0, 0, 0);
				$bgcolor = ImageColorTransparent($new_img, $bgcolor);
			}
			$fun( $simg, $destimg);
		}
        ImageDestroy($simg);
        ImageDestroy($wimg);
        return $result;
    }

    //按比例裁剪
    function create_thumb($source_img_file, $dest_img_file, $new_width, $new_height, $level = 80) {
        $path_parts = pathinfo($source_img_file);
        $ext_name = strtolower($path_parts['extension']);
        $img = null;
        $img = $this->imagecreatefromimg($source_img_file);
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
            $fun = $this->imagecreatefun($ext_name);
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
        } else {
            redirect('global_upload_image_dnot_support');
        }
    }

    /*
    创建缩略图 ，等比例缩放
    按比例，优先使用 new_width 生成，
    new_height 在 $source_img_width > $source_img_height 的情况下 使用
    */
    function create_thumb2($source_img_file, $dest_img_file, $new_width, $new_height, $level = 80) {
        $path_parts = pathinfo($source_img_file);
        $ext_name = strtolower($path_parts['extension']);
        $img = null;
        $img = $this->imagecreatefromimg($source_img_file);
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
            $imgcreate_fun = function_exists('ImageCreateTrueColor') ? 'ImageCreateTrueColor' : 'ImageCreate';
            $new_img = $imgcreate_fun($new_img_width, $new_img_height);
            ImageCopyResampled($new_img, $img, 0, 0, 0, 0, $new_img_width, $new_img_height, $source_img_width, $source_img_height);
            $fun = $this->imagecreatefun($ext_name);
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
        } else {
            redirect('global_upload_image_dnot_support');
        }
    }

    //返回有效的img资源句柄
    function imagecreatefromimg($img) {
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
    function imagecreatefun($ext) {
        switch($ext) {
        case 'gif':
            return 'imagegif';
        case 'png':
            return 'imagepng';
        default:
            return 'imagejpeg';
        }
    }

    //生成UTF-8编码的水印文字
    function convert_text() {
        if(preg_match("/^[a-z0-9\-\_\/]+$/", $this->watermark_text)) return;
        if(_G('charset')=='gb2312') {
            $loader =& _G('loader');
            $loader->lib('chinese',null,false);
            $CHS = new ms_chinese('gb2312','utf-8');
            $this->watermark_text = $CHS->Convert($this->watermark_text);
            unset($CHS);
        }
    }

    function is_anim($srcfile) {
        $fp = fopen($srcfile, 'rb');
        $filecontent = fread($fp, filesize($srcfile));
        fclose($fp);
        return strposex($filecontent, 'NETSCAPE2.0');
    }

}

