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
        /*自动创建需求目录*/
        if (!is_dir($config['sql_file_path'])) {
            mkdir("./{$config['sql_file_path']}");
        }
        if (!is_dir($config['out_path'])) {
            mkdir("./{$config['out_path']}");
        }
        if (!is_dir($config['mysql_cache'])) {
            mkdir("./{$config['mysql_cache']}");
        }
    }

    /**
     * 初始化数据库
     */
    private function _init_pdo ()
    {
        $config = $this->config;
        $this->_db = new PDO($config['dsn'], $config['username'], $config['password']);
        $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        $this->_db->exec('SET NAMES ' . $config['charset']);
    }

    /**
     * @return bool
     */
    public function index ()
    {
        /*单独查看*/
        if (!empty($_GET['mod_show'])
            && !empty($_GET['alone_sql'])
        ) {
            $this->alone_sql_html($_GET['alone_sql']);
            return TRUE;
        }

        /*选择出对比数据*/
        if (!empty($_GET['mod_contrast'])
            && !empty($_GET['alone_sql'])
        ) {
            $this->select_contrast_sql_html($_GET['alone_sql']);
            return TRUE;
        }

        /*对比选择的数据*/
        if (!empty($_GET['contrast'])
            && !empty($_GET['contrast_a'])
            && !empty($_GET['contrast_b'])
        ) {
            $lhs = $_GET['contrast_a'];
            $rhs = $_GET['contrast_b'];
            $this->compare_html($lhs, $rhs);
            return TRUE;
        }

        /*缓存数据结构文件上传处理*/
        if (!empty($_FILES['cache_file'])) {
            echo json_encode($this->_do_cache_file());
            return TRUE;
        }

        /*页面方式选择出要比较的文件*/
        $files = $this->FacilitateFile($this->config['sql_file_path']);
        /*页面选出要找的结构缓存文件*/
        $files_cache = $this->FacilitateFile($this->config['mysql_cache']);

        if (empty($_GET)) {
            $this->select_file_html($files, $files_cache);
            return FALSE;
        } else if (empty($_GET['file_a']) || empty($_GET['file_b'])) {
            echo "<script>alert(\"参数错误，请重新选择或填写！\");window.history.back();</script>";
            return FALSE;
        } else {
            /*比较结果方式调用*/
            $this->_differ_mod();
            return TRUE;
        }
    }

    private function _differ_mod ()
    {
        /*业务比较*/
        $file_a = $_GET['file_a'];
        $file_b = $_GET['file_b'];
        /*判断使用方式*/
        if ($_GET['cache']) {
            /*比较生成零时差异文件*/
            $json = $this->compare($file_a, $file_b, $_GET['cache']);
        } else {
            /*比较生成零时差异文件*/
            $json = $this->compare($file_a, $file_b);
        }
        /*对比结果*/
        $arr = json_decode($json, TRUE);
        $this->compare_file_html($arr['a'], $arr['b']);
    }

    /**
     * @param $sqls
     * @return array
     */
    private function _sql_for_arr ($sqls)
    {
        return explode(";", $sqls);
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
     * 处理数据结构缓存文件
     */
    private function _do_cache_file ()
    {
        $file = 'cache_file';
        set_time_limit(0);//设置响应时间为永久
        if ($_FILES && isset($_FILES[$file])) {
            $fileTmp = $_FILES[$file];
            //判断上传文件时是否有错误
            $error = '';
            $return = array();
            foreach ($fileTmp['error'] AS $i_error => $file_error) {
                if ($file_error[$i_error] > 0) {
                    switch ($file_error[$i_error]) {
                        case '1':
                            $error = '文件过大！限制为：' . ini_get('upload_max_filesize');
                            break;
                        case '2':
                            $error = '文件过大！限制为：' . ini_get('upload_max_filesize');
                            break;
                        case '3':
                            $error = '文件只有部分被上传';
                            break;
                        case '4':
                            $error = '没有文件被上传';
                            break;
                        case '6':
                            $error = '找不到临时文件夹';
                            break;
                        case '7':
                            $error = '文件写入失败';
                            break;
                    }
                    if ($error != '') {
                        $return[] = array('status' => '0', 'data' => $error);
                    }
                }
            }
            if ($return) {
                return $return;
            }

            foreach ($fileTmp['name'] AS $i_name => $file_name) {
                /*获取文件后缀名*/
                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                /*判断是否是我们自定义的类型,如果用户不设置,则使用默认*/
                if (empty($type)) {
                    $type = array('txt', 'sql');
                }
                /*判断文集是否非法*/
                if (!in_array($ext, $type)) {
                    $return[] = array('status' => '0', 'data' => '类型非法');
                }
            }
            if ($return) {
                return $return;
            }

            foreach ($fileTmp['tmp_name'] AS $i_tmp_name => $tmp_name) {
                //检测上传的目标文件夹是否存在,如果不存在,则创建
                $uploadAddr = __DIR__ . "/../../cache";
                $fileName = "{$uploadAddr}/{$fileTmp['name'][$i_tmp_name]}";

                //移动文件,移动成功返回上传以后的文件路径
                $return[] = move_uploaded_file(
                    $tmp_name,
                    $fileName
                ) ? array(
                    'status' => 1, 'data' => $fileName
                ) : array(
                    'status' => '0', 'data' => '文件移动失败'
                );
            }
            return $return;
        } else {
            return array('status' => '0', 'data' => '文件传入失败');
        }
    }

    /**
     * html页面，选择出要比较的两个sql文件
     *
     * @param $files
     * @return string
     */
    private function select_file_html ($files, $files_cache)
    {
        $html_option = '';
        foreach ($files as $file) {
            $html_option .= "<option value=\"{$file}\">{$file}</option>";
        }

        $html_option_cache = '';
        foreach ($files_cache as $file_cache) {
            $html_option_cache .= "<option value=\"{$file_cache}\">{$file_cache}</option>";
        }

        return require_once __DIR__ . "/view/select_file_html.php";
    }

    /**
     * @param $sqls
     * @return mixed
     */
    private function alone_sql_html ($sqls)
    {
        $sql_arr = $this->_sql_for_arr($sqls);
        $rs = "";
        foreach ($sql_arr as $sql) {
            if (!empty(trim($sql))) {
                $tmp_sql = $this->file_str("{$sql};");
                $rs .= $tmp_sql ? "<pre>{$tmp_sql}</pre>" : "insert语句解析失败:'{$sql}'<br>";
            }
        }
        $return = $rs ? "<div>{$rs}</div>" : "\nFALSE\n";
        return require_once __DIR__ . "/view/alone_html.php";
    }

    /**
     * @param $sqls
     * @return mixed
     */
    private function select_contrast_sql_html ($sqls)
    {
        $sql_arr = $this->_sql_for_arr($sqls);
        $rs_a = "";
        $rs_b = "";
        foreach ($sql_arr as $sql) {
            if (!empty($sql)) {
                $tmp_a = $this->file_str("{$sql};");
                $rs_a .= "
                <span><input type=\"radio\" name=\"contrast_a\" value=\"{$tmp_a}\">
                {$sql}</span>
                ";
                $tmp_b = $this->file_str("{$sql};");
                $rs_b .= "
                <span><input type=\"radio\" name=\"contrast_b\" value=\"{$tmp_b}\">
                {$sql}</span>
                ";
            }
        }
        return require_once __DIR__ . "/view/select_contrast_sql_html.php";
    }

    /**
     * @param $lhs
     * @param $rhs
     * @return mixed
     */
    private function compare_html ($lhs, $rhs)
    {
        if (!file_exists("./out/tmp")) {
            mkdir('./out/tmp', 0777, true);
        }
        file_put_contents("./out/tmp/a.txt", $lhs);
        file_put_contents("./out/tmp/b.txt", $rhs);
        return require_once __DIR__ . "/view/compare_html.php";
    }

    /**
     * 利用diff将两个文件差异记录在零时文件中。
     *
     * @param $a
     * @param $b
     * @return string
     */
    private function compare ($a, $b, $cache = '')
    {
        // 如果不是文件则尝试转换编码
        if ($this->config["GBK"]) {
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
            if ($cache) {
                return $this->analysis($a, $b, $cache);
            } else {
                return $this->analysis($a, $b);
            }
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
     * 解析差异的零时文件，分成两类文件。
     *
     * @param $a
     * @param $b
     * @return string
     */
    private function analysis ($a, $b, $cache = '')
    {
        $file_path = "./out/tmp.txt";
        $txt = $this->file_get_contents_ex($file_path);
        $arr = explode("\n", $txt);
        $file_a_str = '';
        $file_b_str = '';
        foreach ($arr as $str) {
            $symbol = substr($str, 0, 1);
            if ($symbol == '<') {
                $file_a_str .= $this->file_str($str, $cache);
            }
            if ($symbol == '>') {
                $file_b_str .= $this->file_str($str, $cache);
            }
        }
        /*写入文件，删除零时文件*/
        $a = pathinfo($a, PATHINFO_FILENAME);
        $b = pathinfo($b, PATHINFO_FILENAME);

        file_put_contents("./out/{$a}-{$b}-{$a}.txt", $file_a_str);
        file_put_contents("./out/{$a}-{$b}-{$b}.txt", $file_b_str);

        unlink("./out/tmp.txt");

        if ($this->config["GBK"]) {
            $a = iconv("GBK", "utf-8", $a);
            $b = iconv("GBK", "utf-8", $b);
        }
        return json_encode([
            'status' => TRUE,
            'error' => "请查看./out目录下：./out/{$a}-{$b}-{$a}.txt ./out/{$a}-{$b}-{$b}.txt",
            'a' => "./out/{$a}-{$b}-{$a}.txt",
            'b' => "./out/{$a}-{$b}-{$b}.txt",
        ]);
    }

    /**
     * @param $str
     * @return string
     */
    private function file_str ($str, $cache = '')
    {
        /*找出表名字*/
        $pattern_table_name = "/INTO `(.*?)` VALUES/";
        if (preg_match($pattern_table_name, $str, $match_table_name)) {
            $table_name = $match_table_name[1];
        }
        /*找出表数据*/
        $pattern_table_value = "/VALUES \((.*?)\);/";
        if (preg_match($pattern_table_value, $str, $match_table_value)) {
            $table_value_arr = $this->analysis_field($match_table_value[1]);
        }

        /*验证*/
        if (empty($table_name)) {
            return FALSE;
        }

        if ($cache) {
            /*获取缓存文件为字符串*/
            $cache_txt = $this->file_get_contents_ex("./{$cache}");
            /*根据换行符切割为数组*/
            $cache_arr = explode("\r\n", $cache_txt);
            /*循环解析*/
            $file_str_row = "--- table {$table_name}----\n\n";
            $i = 0;
            foreach ($cache_arr AS $cache) {
                $cache_tmp = explode("\t", $cache);
                if ($table_name == $cache_tmp[0]) {
                    if (!empty($table_value_arr[$i])) {
                        $file_str_row .= "{$cache_tmp[1]} {$cache_tmp[2]} : {$table_value_arr[$i]}\n";
                    }
                    $i++;
                }
            }
            $file_str_row .= "\n";
        } else {
            /*初始化pdo*/
            $this->_init_pdo();
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
                if (!empty($table_value_arr)) {
                    $file_str_row .= "{$row['COLUMN_NAME']} {$row['COLUMN_COMMENT']} : {$table_value_arr[$i]}\n";
                    $i++;
                }
            }
            $file_str_row .= "\n";
        }

        return $file_str_row;
    }

    /**
     * 解析字段字符串
     *
     * @param $str
     * @return mixed
     */
    private function analysis_field ($str)
    {
        $return_arr = [];
        eval('$return_arr = [' . $str . '];');
        return $return_arr;
    }

    /**
     * @param $a
     * @param $b
     * @return string
     */
    private function compare_file_html ($a, $b)
    {
        return require_once __DIR__ . "/view/commpare_file_html.php";
    }

    /**
     * 尝试gbk、utf-8两种编码；优先尝试传入编码
     *
     * @param $path
     * @return bool|string
     */
    private function file_get_contents_ex ($path)
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