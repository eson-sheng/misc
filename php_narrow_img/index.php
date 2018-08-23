<?php

narrowImg(600, './img', './img2');
//narrowImg(300, './img', './img'); //覆盖到原目录

echo '程序运行完毕。';

/*
 * 缩小指定目录内的图片，并放入指定目录。
 * @param $maxHeight 图片的最大高度（如果图片高度小于此高度，则不进行缩小）
 * @param $fromDir 图片目录
 * @param $toDir 生成目录（如果等于 $fromDir，则覆盖到原目录）
 * */
function narrowImg($maxHeight, $fromDir, $toDir)
{
    if (!file_exists($toDir)) {
        mkdir($toDir);
    }
    $paths = scandir($fromDir);
    foreach ($paths as $path) {
        if ($path === '.' || $path === '..') {
            continue;
        }
        $full_path = $fromDir . '/' . $path;
        if (is_dir($full_path)) {
            if ($toDir === $fromDir) {
                narrowImg($maxHeight, $full_path, $full_path);
            } else {
                narrowImg($maxHeight, $full_path, $toDir . '/' . $path);
            }
            continue;
        }
        $img_resource = null;
        $type_num = exif_imagetype($full_path);
        if(!$type_num){
            continue;
        }
        switch ($type_num) {
            case IMAGETYPE_GIF:
                $img_resource = imagecreatefromgif($full_path);
                break;
            case IMAGETYPE_JPEG:
                $img_resource = imagecreatefromjpeg($full_path);
                break;
            case IMAGETYPE_PNG:
                $img_resource = imagecreatefrompng($full_path);
                break;
            default:
                $not_img=true;
                break;
        }
        $width = imagesx($img_resource);
        $height = imagesy($img_resource);
        $ratio = round($width / $height, 2);
        if ($height > $maxHeight) {
            $new_resource = imagecreatetruecolor($maxHeight * $ratio, $maxHeight);
            imagecopyresized($new_resource, $img_resource,
                0, 0,
                0, 0,
                $maxHeight * $ratio, $maxHeight,
                $width, $height);
            if ($toDir === $fromDir) {//覆盖到原目录
                imagejpeg($new_resource, $full_path);
            } else {//生成到新目录
                imagejpeg($new_resource, $toDir . '/' . $path);
            }
            imagedestroy($new_resource);
        } else {
            if ($toDir !== $fromDir) {//复制到新目录
                copy($full_path, $toDir . '/' . $path);
            }
        }
        imagedestroy($img_resource);
    }
}