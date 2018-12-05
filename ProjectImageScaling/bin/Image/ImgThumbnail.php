<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2018/11/28
 * Time: 下午3:56
 */

namespace Image;

class ImgThumbnail
{

    private $source;
    private $imageinfo;
    private $image;
    private $percent = 0.1;
    private $newImageName;

    /**
     * @param string $source 图片url
     * @param string $percent 默认就可以
     * @param string $newImageName 保存图片的名称
     */
    public function __construct ($source, $percent, $newImageName)
    {
        $this->source = $source;
        $this->percent = $percent;
        $this->newImageName = $newImageName;

        $this->openImage();
        $this->thumpImage();
//        $this->showImage();
        $this->saveImage();
    }

    /**
     * 打开图片
     * @author zhx
     */
    public function openImage ()
    {
        list ($width, $height, $type, $attr) = getimagesize($this->source);

        $this->imageinfo = array(
            'width' => $width,
            'height' => $height,
            'type' => image_type_to_extension($type, false),
            'attr' => $attr
        );

        $fun = "imagecreatefrom" . $this->imageinfo ['type'];
        $this->image = $fun ($this->source);
    }

    /**
     * 操作图片
     * @author zhx
     */
    public function thumpImage ()
    {
        $new_width = $this->imageinfo ['width'] * $this->percent;
        $new_height = $this->imageinfo ['height'] * $this->percent;
        $image_thump = imagecreatetruecolor($new_width, $new_height);
        // 将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->imageinfo ['width'], $this->imageinfo ['height']);
        imagedestroy($this->image);
        $this->image = $image_thump;
    }

    /**
     * 输出图片
     * @author zhx
     */
    public function showImage ()
    {
        header('Content-Type: image/' . $this->imageinfo ['type']);
        $funcs = "image" . $this->imageinfo ['type'];
        $funcs ($this->image);
    }

    /**
     * 保存图片到硬盘
     * @author zhx
     */
    public function saveImage ()
    {
        $funcs = "image" . $this->imageinfo ['type'];
//        $funcs ($this->image, $this->newImageName . '.' . $this->imageinfo ['type']);
        $funcs ($this->image, $this->newImageName);
    }


    /**
     * 销毁图片
     * @author zhx
     */
    public function __destruct ()
    {
        imagedestroy($this->image);
    }
}