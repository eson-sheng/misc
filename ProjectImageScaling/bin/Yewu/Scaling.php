<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2018/11/28
 * Time: 下午4:00
 */

namespace Yewu;

use Image\ImgThumbnail;

class Scaling
{
    /*配置属性*/
    public $config = array();

    /**
     * Scaling constructor.
     * @param $config
     */
    public function __construct ($config)
    {
        $this->config = $config;
    }

    public function index ()
    {
        /*备份图片*/
        $this->_backup($this->config['img_src'], $this->config['img_backup']);
        /*缩放图片*/
        $this->_zoom($this->config['img_src'], $this->config['img_src_out']);

        echo json_encode(['status' => TRUE]);
    }

    /**
     * 递归式便利目录文件获取路径
     *
     * @param $path
     * @param array $files
     * @return array
     */
    private function FacilitateFile ($path, &$files = [])
    {
        $handle = opendir($path);//打开目录句柄
        if ($handle) {
            while (($file = readdir($handle)) == true) {
                if ($file != '.' && $file != '..') {
                    $p = "{$path}/{$file}";
                    if (is_dir($p)) {
                        $this->FacilitateFile($p, $files);
                    } else {
                        if ($this->config["GBK"]) {
                            $p = iconv("gbk", "utf-8", $p);
                        }
                        $files[] = $p;
                    }
                }
            }
        }
        return $files;
    }

    /**
     * @param $url
     * @param $url_backup
     * @return bool
     */
    private function _backup ($url, $url_backup)
    {
        $paths = $this->FacilitateFile($url);
        foreach ($paths as $path) {
            $file_path = str_replace($url, '', $path);
            $file_back = "{$url_backup}{$file_path}";
            if (!is_file($file_back)) {
                copy($path, $file_back);
            }
        }
        return TRUE;
    }

    /**
     * @param $url
     * @param $url_out
     * @return bool
     */
    private function _zoom ($url, $url_out)
    {
        $paths = $this->FacilitateFile($url);
        foreach ($paths as $path) {
            $file_path = str_replace($url, '', $path);
            $file_back = "{$url_out}{$file_path}";
            new ImgThumbnail($path, 0.3, $file_back);
        }
        return TRUE;
    }
}