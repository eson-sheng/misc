<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2018/11/5
 * Time: 上午10:51
 */

namespace Analysis;

use PDO;

/**
 * Class Diff
 * @package Analysis
 */
class Diff
{
    /*配置属性*/
    public $config = array();
    /*数据库*/
    private $_db = null;

    /**
     * Diff constructor.
     * @param $config
     */
    public function __construct ($config)
    {
        $this->config = $config;
        $this->_db = new PDO($config['dsn'], $config['username'], $config['password']);
        $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        $this->_db->exec('SET NAMES ' . $config['charset']);
    }

    /**
     *
     */
    public function index ()
    {
        /*页面方式选择出要比较的文件*/
        $files = $this->FacilitateFile($this->config['sql_file_path']);
        $html = $this->select_file_html($files);

        if (empty($_POST)) {
            echo $html;
        } else if (empty($_POST['file_a']) || empty($_POST['file_b'])) {
            $html .= "<script>alert(\"参数错误，请重新选择\");</script>";
            echo $html;
        } else {
            /*业务比较*/
            $file_a = $_POST['file_a'];
            $file_b = $_POST['file_b'];
            /*比较生成零时差异文件*/
            $json = $this->compare($file_a, $file_b);
            echo $json;
        }
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
                        $enc = mb_detect_encoding($p, "gb2312", true);
                        if ($enc === 'EUC-CN') {
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
     * html页面，选择出要比较的两个sql文件
     *
     * @param $files
     * @return string
     */
    private function select_file_html ($files)
    {
        $html_option = '';
        foreach ($files as $file) {
            $html_option .= "<option value=\"{$file}\">{$file}</option>";
        }
        $html = <<<EOF
        <!DOCTYPE html>
        <html lang="zh">
            <head>
                <meta charset="UTF-8">
                <title>请选择需要对比的sql文件</title>
            </head>
            <body>
                <div>
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <select name="file_a" id="file_a">
                            <option value="">==请选择==</option>
                            {$html_option}
                        </select>
                        <select name="file_b" id="file_b">
                            <option value="">==请选择==</option>
                            {$html_option}
                        </select>
                        <input type="submit" id="submit" value="提交">
                        <input type="reset"  id="reset" value="重置">
                    </form>
                </div>
            </body>
        </html>
EOF;
        return $html;
    }

    /**
     * @param $a
     * @param $b
     * @return array
     */
    private function compare ($a, $b)
    {
        // 如果不是文件则尝试转换编码
        if (!is_file($a)) {
            $a = iconv("utf-8", "gbk", $a);
            $b = iconv("utf-8", "gbk", $b);
        }
        if (!is_file($a)) {
            return json_encode([
                'status' => FALSE,
                'error' => "文件:'$a'不存在！",
            ]);
        }
        if (!is_file($b)) {
            return json_encode([
                'status' => FALSE,
                'error' => "文件:'$b'不存在！",
            ]);
        }
        $this->shell(["diff {$a} {$b} > ./out/tmp.txt"]);
        if (is_file(__DIR__ . "/../../out/tmp.txt")) {
            return $this->analysis($a, $b);
        } else {
            return json_encode([
                'status' => FALSE,
                'error' => '差异文件创建失败！',
            ]);
        }
    }

    /**
     * 执行shell命令
     *
     * @param $cmds
     * @return string
     */
    private function shell ($cmds)
    {
        $tmp_return = "\n";
        foreach ($cmds as $cmd) {
            $tmp_return .= shell_exec("{$cmd} 2>&1");
            $tmp_return .= "\n";
        }
        return $tmp_return;
    }

    /**
     * @return array
     */
    private function analysis ($a, $b)
    {
        $file_path = "./out/tmp.txt";
        $txt = $this->file_get_contents_ex($file_path);
        $arr = explode("\n", $txt);
        $file_a_str = '';
        $file_b_str = '';
        foreach ($arr as $str) {
            $symbol = substr($str, 0, 1);
            if ($symbol == '<') {
                $file_a_str .= $this->file_str($str);
            }
            if ($symbol == '>') {
                $file_b_str .= $this->file_str($str);
            }
        }
        /*写入文件，删除零时文件*/
        $a = pathinfo($a, PATHINFO_FILENAME);
        $b = pathinfo($b, PATHINFO_FILENAME);

        file_put_contents("./out/{$a}-{$b}-{$a}.txt", $file_a_str);
        file_put_contents("./out/{$a}-{$b}-{$b}.txt", $file_b_str);

        unlink("./out/tmp.txt");

        return json_encode([
            'status' => TRUE,
            'error' => '请查看./out目录',
        ]);
    }

    /**
     * @param $str
     * @return string
     */
    private function file_str ($str)
    {
        /*找出表名字*/
        $pattern_table_name = "/INTO `(.*?)` VALUES/";
        if (preg_match($pattern_table_name, $str, $match_table_name)) {
            $table_name = $match_table_name[1];
        }
        /*找出表数据*/
        $pattern_table_value = "/VALUES \((.*?)\);/";
        if (preg_match($pattern_table_value, $str, $match_table_value)) {
            $table_value_arr = explode(",", $match_table_value[1]);
        }
        /*找出表头名称*/
        $sql = "
        SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.Columns WHERE table_name='{$table_name}' AND table_schema='{$this->config['database']}';
        ";
        $query = $this->_db->prepare($sql);
        $query->execute();
        $ret = $query->fetchAll();
        /*数据拼接*/
        $file_str_row = "--- table {$table_name}----\n\n";
        $i = 0;
        foreach ($ret as $row) {
            $file_str_row .= "{$row['COLUMN_NAME']} {$row['COLUMN_COMMENT']} : {$table_value_arr[$i]}\n";
            $i++;
        }
        $file_str_row .= "\n";
        return $file_str_row;
    }


    // 尝试gbk、utf-8两种编码；优先尝试传入编码
    private function file_get_contents_ex($path)
    {
        if (is_file($path)) {
            return file_get_contents($path);
        }
        $enc = mb_detect_encoding($path, "gb2312", true);
        if ($enc === 'EUC-CN') {
            $path2 = iconv("gbk", "utf-8", $path);
        } else {
            $path2 = iconv("utf-8", "gbk", $path);
        }
        if (is_file($path2)) {
            return file_get_contents($path2);
        }
        return false;
    }

}