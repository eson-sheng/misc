<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2018-12-20
 * Time: 17:44
 */

require_once __DIR__ . "/class.phpmailer.php";
require_once __DIR__ . "/class.smtp.php";

class Email
{
    /*配置属性*/
    public $config = array();
    /*数据库*/
    private $_db = null;

    public function __construct ($config)
    {
        $this->_config = $config;
//        $this->init();
    }

    public function init ()
    {
        /*实例化PDO数据库连接*/
        $this->_db = new PDO(
            $this->_config['dsn'],
            $this->_config['username'],
            $this->_config['passwd'],
            array(
                /*错误异常模式处理*/
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                /*返回一个索引为结果集列名的数组*/
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                /*设置PDO属性预处理语句模拟*/
                PDO::ATTR_EMULATE_PREPARES => FALSE,
                /*初始化字符集*/
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            )
        );
    }

    public function index ()
    {
//        /*邮件页面文本*/
//        ob_start();
//        require_once __DIR__ . '/view/txt.php';
//        $msg = ob_get_contents();
//        ob_end_clean();
//
//        /*发送对象赋值*/
//        $to = '834767372@qq.com';
//        $subject = '田馥甄-2017巡回演唱会PLUS成都站';
//
//        echo $this->_send($to, $subject, $msg);

        ob_start();
        require_once __DIR__ . '/view/index_html.php';
        $html = ob_get_contents();
        ob_end_clean();

        if (
            !empty($_POST['to']) &&
            !empty($_POST['subject']) &&
            !empty($_POST['msg'])
        ) {
            $html = $this->_send(
                $_POST['to'],
                $_POST['subject'],
                $_POST['msg']
            );
        }

        echo $html;
    }

    private function _send ($to, $subject, $msg)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'HTML';
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.163.com';
        $mail->Port = '465';
        $mail->CharSet = "utf-8";
        $mail->SMTPSecure = 'ssl';

        //配置
        $mail->Username = $this->_config['uname'];
        $mail->Password = $this->_config['passd'];
        $mail->setFrom(
            $this->_config['address'],
            $this->_config['name']
        );

        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->MsgHTML($msg);

        if (!$mail->send()) {
            return json_encode([
                'status' => FALSE,
                'errmsg' => "Mailer Error: " . $mail->ErrorInfo,
            ]);
        } else {
            return json_encode([
                'status' => TRUE,
                'errmsg' => "Message sent!",
            ]);
        }
    }
}