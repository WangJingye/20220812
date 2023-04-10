<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/9/4
 * Time: 14:33
 */

namespace App\Model;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class QQMailer
{
    public static $HOST = 'smtp.qq.com'; // QQ 邮箱的服务器地址
    public static $PORT = 465; // smtp 服务器的远程服务器端口号
    public static $SMTP = 'ssl'; // 使用 ssl 加密方式登录
    public static $CHARSET = 'UTF-8'; // 设置发送的邮件的编码

    private static $USERNAME = '80656328@qq.com'; // 授权登录的账号
    private static $PASSWORD = 'bfuucxihgijxbgdj'; // 授权登录的密码

    /**
     * QQMailer constructor.
     * @param bool $debug [调试模式]
     */
    public function __construct($debug = false)
    {
        $this->mailer = new PHPMailer();
        $this->mailer->SMTPDebug = $debug ? 1 : 0;
        $this->mailer->isSMTP(); // 使用 SMTP 方式发送邮件
    }

    /**
     * @return PHPMailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    private function loadConfig()
    {
        /* Server Settings  */
        $this->mailer->SMTPAuth = true; // 开启 SMTP 认证
        $this->mailer->Host = self::$HOST; // SMTP 服务器地址
        $this->mailer->Port = self::$PORT; // 远程服务器端口号
        $this->mailer->SMTPSecure = self::$SMTP; // 登录认证方式
        /* Account Settings */
        $this->mailer->Username = self::$USERNAME; // SMTP 登录账号
        $this->mailer->Password = self::$PASSWORD; // SMTP 登录密码
        $this->mailer->From = self::$USERNAME; // 发件人邮箱地址
        $this->mailer->FromName =  '内部POS文件核对'; // 发件人昵称（任意内容）
        /* Content Setting  */
        $this->mailer->isHTML(true); // 邮件正文是否为 HTML
        $this->mailer->CharSet = self::$CHARSET; // 发送的邮件的编码
    }

    /**
     * Add attachment
     * @param $path [附件路径]
     */
    public function addFile($path)
    {
        try{
            $this->mailer->addAttachment($path);
        }
        catch (\Exception $e){
            echo $e;
        }
    }


    /**
     * 发送给主收件人
     * @param $email
     * @param $title
     * @param $content
     * @return bool
     * @throws Exception
     */
    public function send($email, $title, $content)
    {
        $this->loadConfig();
        try{
            $this->mailer->addAddress($email); // 收件人邮箱
            $this->mailer->addCC("alice.tang2@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("jue.wang@super-staffing.com"); // 抄送邮箱
            $this->mailer->addCC("sheng.huang@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("david.jin@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("kris.zhu@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("phil.song@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("jason.jin@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("Peien.Wang@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("yan.jiang@connext.com.cn"); // 抄送邮箱

        }
        catch (\Exception $exception){
            echo $exception;
        }
        $this->mailer->Subject = $title; // 邮件主题
        $this->mailer->Body = $content; // 邮件信息
        return (bool)$this->mailer->send(); // 发送邮件
    }


    /**
     * 这个是处理WMS新上传文件时候的邮件发送
     * @param $email
     * @param $title
     * @param $content
     * @return bool
     * @throws Exception
     */
    public function wmsEmailSend($email, $title, $content)
    {
        $this->loadConfig();
        try{
            $this->mailer->addAddress($email); // 收件人邮箱
            $this->mailer->addCC("kris.zhu@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("phil.song@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("jason.jin@connext.com.cn"); // 抄送邮箱
            $this->mailer->addCC("yan.jiang@connext.com.cn"); // 抄送邮箱

        }
        catch (\Exception $exception){
            echo $exception;
        }
        $this->mailer->Subject = $title; // 邮件主题
        $this->mailer->Body = $content; // 邮件信息
        return (bool)$this->mailer->send(); // 发送邮件
    }
}