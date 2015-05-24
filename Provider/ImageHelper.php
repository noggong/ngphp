<?php

class ImageHelper
{

    /**
     * @param $path
     * @return bool
     */
    public static function isImage($path)
    {
        $a = @getimagesize($path);
        $image_type = $a[2];

        if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
        {
            return true;
        }
        return false;
    }
    /**
     * @param $srcPath
     */
    public static function writeBinary($srcPath)
    {
        $src_info = getimagesize($srcPath);
        if ($src_info[2] == 1) {
            $src = ImageCreateFromGIF($srcPath);
            header('Content-Type: image/gif');
            Imagegif($src);
            imageDestroy($src);

        } elseif ($src_info[2] == 2) {
            $src = ImageCreateFromJPEG($srcPath);
            header('Content-Type: image/jpeg');
            Imagejpeg($src);
            imageDestroy($src);

        } elseif ($src_info[2] == 3) {
            $src = ImageCreateFromPNG($srcPath);
            header('Content-Type: image/png');
            Imagepng($src);
            imageDestroy($src);
        }

        exit();
    }


    /**
     * @param string $src
     * @param string $dest_path
     * @param string $file_name
     * @return bool|string
     */
    public static function uploadImage($src, $dest_path, $file_name)
    {

        self::checkDir($dest_path);
        if (copy($src, $dest_path . $file_name)) {
            unlink($src);
            return $dest_path . $file_name;
        } else {
            return false;
        };
    }

    /**
     * 받은 path 를 root 부터 따라가며 없는 디렉토리를 생성해준다
     * @param string $path
     */
    public static function checkDir($path)
    {
        $path_array = explode('/', $path);
        $path_string = '';
        foreach ($path_array as $dir) {
            $path_string .= '/' . $dir;
            if (file_exists($path_string)) {
                continue;
            } else {

                $olodmask = umask(0);
                mkdir($path_string, 0777);
                umask($olodmask);
            }

        }
    }

    /**
     * @param $srcPath
     * @param $rewidth
     * @param $reheight
     * @param array $filter
     * @param bool $marginFill
     */
    public static function writeThumbNeilBinary($srcPath, $rewidth, $reheight, array $filter = null, $marginFill = true)
    {
        $src_info = getimagesize($srcPath);

        $dimen_w = floatval($rewidth / $src_info[0]);
        $dimen_h = floatval($reheight / $src_info[1]);
        $dimen = $dimen_w;

        if ($src_info[0] < $src_info[1]) { // 원본에서 세로가 더 클때
            $dimen = $dimen_h;
        }

        $rewidth_img = round($src_info[0] * $dimen);
        $reheight_img = round($src_info[1] * $dimen);
        if ($rewidth_img > $src_info[0]) $rewidth_img = $src_info[1];
        if ($reheight_img > $src_info[1]) $reheight_img = $src_info[1];

        // 리사이즈 크면 원본 유지
        if ($rewidth_img > $rewidth) {
            $reheight_img = $rewidth;
        }
        if ($rewidth_img > $reheight) {
            $rewidth_img = $reheight;
        }

        $dst = imageCreatetrueColor($rewidth, $reheight);
        $white = ImageColorAllocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        $start_x = round(($rewidth - $rewidth_img) / 2) - 1;
        $start_y = round(($reheight - $reheight_img) / 2) - 1;
        if ($start_x < 0) $start_x = 0;
        if ($start_y < 0) $start_y = 0;
        $end_x = $start_x + $rewidth_img;
        $end_y = $start_y + $reheight_img;
        //echo 'start:'.$start_x .'/'.$start_y.',end:'.$end_x .'/'.$end_y.',orign:'. $src_info[0] .','. $src_info[1].',re:'.$rewidth_img .'/'.$reheight_img;
        //exit;

        if ($src_info[2] == 1) {
            $src = ImageCreateFromGIF($srcPath);
            imagecopyResampled($dst, $src, $start_x, $start_y, 0, 0, $rewidth_img, $reheight_img, imagesx($src), imagesy($src));

            if (!empty($filter)) self::setFilter($dst, $start_x, $start_y, $end_x, $end_y, $filter);

            header('Content-Type: image/gif');
            Imagegif($dst);

        } elseif ($src_info[2] == 2) {
            $src = ImageCreateFromJPEG($srcPath);
            imagecopyResampled($dst, $src, $start_x, $start_y, 0, 0, $rewidth_img, $reheight_img, imagesx($src), imagesy($src));

            if (!empty($filter)) self::setFilter($dst, $start_x, $start_y, $end_x, $end_y, $filter);

            header('Content-Type: image/jpeg');
            Imagejpeg($dst);

        } elseif ($src_info[2] == 3) {
            $src = ImageCreateFromPNG($srcPath);
            imagecopyResampled($dst, $src, $start_x, $start_y, 0, 0, $rewidth_img, $reheight_img, imagesx($src), imagesy($src));

            if (!empty($filter)) self::setFilter($dst, $start_x, $start_y, $end_x, $end_y, $filter);

            header('Content-Type: image/png');
            Imagepng($dst);

        }

        imageDestroy($src);
        imageDestroy($dst);

        // binary 를 write 하기때문에 function 뒤에 write 하는 부분이 있으면 주의 해야함.
        exit();
    }


    /**
     * @param $srcPath 리사이징할 가로 사이즈
     * @param $rewidth 리사이징할 세로 사이즈
     * @param bool $reheight 화일업로드 경로
     * @param array $filter 리사이징할 화일원본
     * @param bool $marginFill 리사이징 후 화일명
     */
    public static function writeThumbNeilBinaryV2($srcPath, $rewidth, $reheight = false, array $filter = null, $marginFill = true)
    {
        $src_info = getimagesize($srcPath);


        if ($reheight == false) {
            $percent = $rewidth / $src_info[0];
            $reheight = $src_info[1] * $percent;
        }


        //s: 원본보다 리사이즈 이미지가 클경우 원본이미지로 썸세일 생성
        if ($rewidth > $src_info[0]) {
            $rewidth = $src_info[0];
        }
        if ($reheight > $src_info[1]) {
            $reheight = $src_info[1];
        }
        //e: 원본보다 리사이즈 이미지가 클경우 원본이미지로 썸세일 생성
        $dimen_w = floatval($rewidth / $src_info[0]);
        $dimen_h = floatval($reheight / $src_info[1]);
        $dimen = $dimen_w;

        if ($src_info[0] < $src_info[1]) { // 원본에서 세로가 더 클때
            $dimen = $dimen_h;
        }

        $rewidth_img = round($src_info[0] * $dimen);
        $reheight_img = round($src_info[1] * $dimen);
        if ($rewidth_img > $src_info[0]) $rewidth_img = $src_info[1];
        if ($reheight_img > $src_info[1]) $reheight_img = $src_info[1];

        // 리사이즈 크면 원본 유지
        if ($rewidth_img > $rewidth) {
            $reheight_img = $reheight;
        }
        if ($rewidth_img > $reheight) {
            $rewidth_img = $rewidth;
        }
        $dst = imageCreatetrueColor($rewidth, $reheight);
        $white = ImageColorAllocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        $start_x = round(($rewidth - $rewidth_img) / 2) - 1;
        $start_y = round(($reheight - $reheight_img) / 2) - 1;
        if ($start_x < 0) $start_x = 0;
        if ($start_y < 0) $start_y = 0;
        $end_x = $start_x + $rewidth_img;
        $end_y = $start_y + $reheight_img;
        //echo 'start:'.$start_x .'/'.$start_y.',end:'.$end_x .'/'.$end_y.',orign:'. $src_info[0] .','. $src_info[1].',re:'.$rewidth_img .'/'.$reheight_img;
        //exit;
        if ($src_info[2] == 1) {
            $src = ImageCreateFromGIF($srcPath);
            imagecopyResampled($dst, $src, $start_x, $start_y, 0, 0, $rewidth_img, $reheight_img, imagesx($src), imagesy($src));

            if (!empty($filter)) self::setFilter($dst, $start_x, $start_y, $end_x, $end_y, $filter);

            header('Content-Type: image/gif');
            Imagegif($dst);

        } elseif ($src_info[2] == 2) {
            $src = ImageCreateFromJPEG($srcPath);
            imagecopyResampled($dst, $src, $start_x, $start_y, 0, 0, $rewidth_img, $reheight_img, imagesx($src), imagesy($src));

            if (!empty($filter)) self::setFilter($dst, $start_x, $start_y, $end_x, $end_y, $filter);

            header('Content-Type: image/jpeg');
            Imagejpeg($dst);

        } elseif ($src_info[2] == 3) {
            $src = ImageCreateFromPNG($srcPath);
            imagecopyResampled($dst, $src, $start_x, $start_y, 0, 0, $rewidth_img, $reheight_img, imagesx($src), imagesy($src));

            if (!empty($filter)) self::setFilter($dst, $start_x, $start_y, $end_x, $end_y, $filter);

            header('Content-Type: image/png');
            Imagepng($dst);

        } else {
            $noimage = '/tank1/board/no_support_320.jpg';
            $file_size = filesize($file);
            @header('Content-Type: image/jpeg');
            @header('Content-Length: ' . $file_size);
            echo file_get_contents($noimage);

            exit();
        }
        imageDestroy($src);
        imageDestroy($dst);

        // binary 를 write 하기때문에 function 뒤에 write 하는 부분이 있으면 주의 해야함.
        exit();
    }


    /**
     * @param $res
     * @param $start_x
     * @param $start_y
     * @param $end_x
     * @param $end_y
     * @param array $filter
     * @return mixed
     */
    public static function setFilter($res, $start_x, $start_y, $end_x, $end_y, array $filter)
    {

        $width = $end_x - $start_x;
        $height = $end_y - $start_y;

        $fil_res = imageCreatetrueColor($width, $height);
        $transparent_color = imagecolorallocatealpha($fil_res, 152, 0, 0, 127);
        imagefill($fil_res, 0, 0, $transparent_color);

        $cross_width = $width;
        $cross_height = $height;
        if ($cross_width > $cross_height) $cross_width = $cross_height;
        if ($cross_width < $cross_height) $cross_height = $cross_width;
        $start_x = $start_x + round(($width - $cross_width) / 2);
        $start_y = $start_y + round(($height - $cross_height) / 2);

        for ($i = 0; $i < count($filter); $i++) {
            $filter_name = $filter[$i];
            switch ($filter_name) {
                case 'CROSS_BLACK_X_ALPHA50':
                    $black = imagecolorallocatealpha($fil_res, 0, 0, 0, 50);
                    imageline($fil_res, 0, 0, $cross_width, $cross_height, $black);
                    imageline($fil_res, 0, 1, $cross_width, $cross_height + 1, $black);
                    imageline($fil_res, 0, $cross_height, $cross_width, 0, $black);
                    imageline($fil_res, 0, $cross_height + 1, $cross_width, 1, $black);
                    imagecopy($res, $fil_res, $start_x, $start_y, 0, 0, $width, $height);
                    imagedestroy($fil_res);
                    break;
            }
        }

        return $res;
    }


    /**
     *  이미지 파일 리사이즈 하여 저장
     * 가로세로 중 하나만 넘어올경우 정비례 비율로 넘어온 한축의 값에 따라 축소, 확대
     * @param $srcimg
     * @param null $dstimg
     * @param int $rewidth
     * @param int $reheight
     * @return bool
     */
    static function ImgResize($srcimg, $dstimg = null, $rewidth = 0, $reheight = 0)
    {
        if (!file_exists($srcimg) && !get_headers($srcimg)) {
            return false;
        }
        if (!self::isImage($srcimg)) {
            return false;
        }
        if ($rewidth == 0 && $reheight == 0) {
            return false;
        }

        list($width, $height, $imgtype) = getimagesize($srcimg);

        if ($rewidth == 0 || $reheight == 0) { // 가로 세로 값중 하나만 넘어온경우 처리
            if ($rewidth != 0) { // 가로값 넘어온경우
                $percent = $rewidth / $width;
                $reheight = $height * $percent;
            }
            if ($reheight != 0) { // 세로값 넘어온경우
                $percent = $reheight / $height;
                $rewidth = $width * $percent;
            }
    }

        $dst = imageCreatetrueColor($rewidth, $reheight);
        if ($imgtype == 1) {
            $src = ImageCreateFromGIF($srcimg);
            imagecopyResampled($dst, $src, 0, 0, 0, 0, $rewidth, $reheight, ImageSX($src), ImageSY($src));
            if (!empty($dstimg)) {
                imagegif($dst, $dstimg);
            } else {
                header('Content-Type: image/gif');
                Imagegif($dst);
            }
            imageDestroy($src);
        } elseif ($imgtype == 2) {
            $src = ImageCreateFromJPEG($srcimg);
            imagecopyResampled($dst, $src, 0, 0, 0, 0, $rewidth, $reheight, ImageSX($src), ImageSY($src));
            if (!empty($dstimg)) {
                imagejpeg($dst, $dstimg, 90);
            } else {
                header('Content-Type: image/jpeg');
                Imagejpeg($dst);
            }
            imageDestroy($src);
        } elseif ($imgtype == 3) {
            $src = ImageCreateFromPNG($srcimg);
            imagecopyResampled($dst, $src, 0, 0, 0, 0, $rewidth, $reheight, ImageSX($src), ImageSY($src));
            if (!empty($dstimg)) {
                imagepng($dst, $dstimg, 4);
            } else {
                header('Content-Type: image/png');
                Imagepng($dst);
            }
            imageDestroy($src);
        }

        if (!empty($dstimg)) {
            imageDestroy($dst);
        }

        return true;
    }

    /**
     * @param $srcPath
     * @param $savePath
     * @param $rewidth
     * @param $reheight
     * @param array $filter
     * @param bool $marginFill
     * @return bool
     */
    public static function makeThumbNeilBinary($srcPath, $savePath, $rewidth, $reheight, array $filter = null, $marginFill = true)
    {
        $src_info = getimagesize($srcPath);

        $dimen_w = floatval($rewidth / $src_info[0]);
        $dimen_h = floatval($reheight / $src_info[1]);
        $dimen = ($src_info[0] < $src_info[1]) ? $dimen_w : $dimen_h;
        $rewidth_img = round($src_info[0] * $dimen);
        $reheight_img = round($src_info[1] * $dimen);

        if ($rewidth_img > $src_info[0]) {
            $rewidth_img = $src_info[1];
        }
        if ($reheight_img > $src_info[1]) {
            $reheight_img = $src_info[1];
        }

        // 리사이즈 크면 원본 유지
        /*
        if ($rewidth_img>$rewidth) {
            $reheight_img = $rewidth;
        }
        if ($rewidth_img>$reheight) {
            $rewidth_img  = $reheight;
        }
        */


        $dst = imageCreatetrueColor($rewidth, $reheight);
        $white = ImageColorAllocate($dst, 255, 255, 255);
        if ($marginFill) {
            imagefill($dst, 0, 0, $white);
        }

        $start_x = round(($rewidth - $rewidth_img) / 2) - 1;
        $start_y = round(($reheight - $reheight_img) / 2) - 1;

        if ($start_x < 0) $start_x * -1;
        if ($start_y < 0) $start_y * -1;
        $end_x = $start_x + $rewidth_img;
        $end_y = $start_y + $reheight_img;
        //echo 'start:'.$start_x .'/'.$start_y.',end:'.$end_x .'/'.$end_y.',orign:'. $src_info[0] .','. $src_info[1].',re:'.$rewidth_img .'/'.$reheight_img;
        //exit;
        if ($end_y < $reheight) {
            $start_y = 0;
            $end_y = $reheight;
        }

        if ($src_info[2] == 1) {
            $src = ImageCreateFromGIF($srcPath);
            imagecopyResampled($dst, $src, $start_x, $start_y, 0, 0, $rewidth_img, $reheight_img, imagesx($src), imagesy($src));

            if (!empty($filter)) self::setFilter($dst, $start_x, $start_y, $end_x, $end_y, $filter);

            $result = Imagegif($dst, $savePath);

        } elseif ($src_info[2] == 2) {
            $src = ImageCreateFromJPEG($srcPath);
            imagecopyResampled($dst, $src, $start_x, $start_y, 0, 0, $rewidth_img, $reheight_img, imagesx($src), imagesy($src));

            if (!empty($filter)) self::setFilter($dst, $start_x, $start_y, $end_x, $end_y, $filter);

            $result = Imagejpeg($dst, $savePath, 90);

        } elseif ($src_info[2] == 3) {
            $src = ImageCreateFromPNG($srcPath);
            imagecopyResampled($dst, $src, $start_x, $start_y, 0, 0, $rewidth_img, $reheight_img, imagesx($src), imagesy($src));

            if (!empty($filter)) self::setFilter($dst, $start_x, $start_y, $end_x, $end_y, $filter);

            $result = Imagepng($dst, $savePath, 4);

        }

        imageDestroy($src);
        imageDestroy($dst);

        // binary 를 write 하기때문에 function 뒤에 write 하는 부분이 있으면 주의 해야함.
        return $result;
    }

    /**
     * 이미지 rotate 에 따라 가로세로 회전후 등록
     * @access public
     * @param array $attach input type으로 전달받은 $_FILES
     * @return void
     */
    public static function checkImageRotate($attach)
    {

        $exif = exif_read_data($attach['tmp_name']);
        //error_log(print_r($exif,true));
        if (!empty($exif['Orientation'])) {
            $extArr = explode('/', $exif['MimeType']);
            $ext = $extArr[1];

            if ($ext == "jpg" || $ext == "jpeg") {
                $image = imagecreatefromjpeg($attach['tmp_name']);
            } else if ($ext == "png") {
                $image = imagecreatefrompng($attach['tmp_name']);
            } else if ($ext == "gif") {
                $image = imagecreatefromgif($attach['tmp_name']);
            }

            switch ($exif['Orientation']) {
                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;
            }
            if ($ext == "jpg" || $ext == "jpeg") {
                imagejpeg($image, $attach['tmp_name']);
            } else if ($ext == "png") {
                imagepng($image, $attach['tmp_name']);
            } else if ($ext == "gif") {
                imagegif($image, $attach['tmp_name']);
            }

        }
    }

    /**
     * @param string $url
     * @return string
     */
    public static function urlencode($url)
    {
        return base64_encode(urlencode($url));
    }

    /**
     * @param string $url
     * @return string
     */
    public static function urldecode($url)
    {

        return urldecode(base64_decode($url));
    }

    /**
     * 이미지를 저장할 path를 분기 하기 위하여 사용.
     * @param string $string
     * @return int
     */
    public static function getUrlImageHash($string)
    {
        $hash = substr(md5($string), 0, 4);
        return base_convert($hash, 16, 2) % 32;

    }
}

?>
