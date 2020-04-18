<?php
namespace Stu\Model;

use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\View\Helper\ServerUrl;


class Sendmsg
{
    public function validatemail($email)
    {
        $end = md5($email);
        return $end;
    }

    public function sendsmtphtmlmail($recv)
    {
        $msg = new Message();
        $msg->setFrom("956662445@qq.com", "cry")
            ->setTo($recv['emailaddr'], $recv['emailname'])
            ->setSubject($recv['subject']);
        $url = new ServerUrl();
        $content = '点击<a href="'.$url->__invoke().'/stu/register/activemail/active/'.$recv['justifyurl'].'">验证邮箱</a>或手动访问'.$url->__invoke().'/stu/register/activemail/active/'.$recv['justifyurl'];
        $html = new MimePart($content);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));
        $msg->setBody($body);


        $smtpOpt = new SmtpOptions(array(
            'name' => 'smtp.qq.com',
            'host' => 'smtp.qq.com',//qq的免费邮箱服务器
            'port' => 25,
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => '956662445@qq.com',    //申请了字符邮箱名的字符邮箱名
                'password' => 'gambzfmrpidebcib',        //邮箱登陆密码 是授权码！！！！
            )
        ));
        $transport = new Smtp();
        $transport->setOptions($smtpOpt);
        $res = $transport->send($msg);
        return $res;
    }
    public function sendsmtpmail($recv)
    {
        $msg = new Message();
        $msg->setFrom("956662445@qq.com", "chen")
            ->setTo($recv['emailaddr'], $recv['emailname'])
            ->setSubject($recv['subject'])
            ->setBody($recv['message']);
        /*
            $smtpOpt1 = new SmtpOptions(array(
                'name' => 'exmail.qq.com',
                'host' => 'smtp.exmail.qq.com',//qq的免费邮箱服务器
                'port' => 25,
                'connection_class' => 'login',
                'connection_config' => array(
                    'username' => 'yzbmail@bjfu.edu.cn',    //申请了字符邮箱名的字符邮箱名
                    'password' => 'BFUYZ1qaz2wsx!',        //邮箱登陆密码 是授权码！！！！
                )

            ));

            $smtpOpt2 = new SmtpOptions(array(
                'name' => 'exmail.qq.com',
                'host' => 'smtp.exmail.qq.com',//qq的免费邮箱服务器
                'port' => 25,
                'connection_class' => 'login',
                'connection_config' => array(
                    'username' => 'yzbmail2.bjfu.edu.cn',    //申请了字符邮箱名的字符邮箱名
                    'password' => 'Beilin123',        //邮箱登陆密码 是授权码！！！！
                )

            ));

            $smtpOpt3 = new SmtpOptions(array(
                'name' => 'exmail.qq.com',
                'host' => 'smtp.exmail.qq.com',//qq的免费邮箱服务器
                'port' => 25,
                'connection_class' => 'login',
                'connection_config' => array(
                    'username' => 'yzbmail3.bjfu.edu,cn',    //申请了字符邮箱名的字符邮箱名
                    'password' => 'Beilin123',        //邮箱登陆密码 是授权码！！！！
                )

            ));

            if ($trans = new Smtp()) {
                switch ((int)$recv['addr'] % 3) {
                    case 0:
                        $trans->setOptions($smtpOpt1);
                        break;
                    case 1:
                        $trans->setOptions($smtpOpt2);
                        break;
                    case 2:
                        $trans->setOptions($smtpOpt3);
                        break;
                    default:
                        $trans->setOptions($smtpOpt1);
                        break;
                }
                //$trans->send($msg);
                return (int)$recv['addr'] % 3;
            } else {
                return (int)$recv['addr'] % 3;
            }*/
        $transport = new Smtp();
        $smtpOpt = new SmtpOptions(array(
            'name' => 'smtp.qq.com',
            'host' => 'smtp.qq.com',//qq的免费邮箱服务器
            'port' => 25,
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => '956662445@qq.com',    //申请了字符邮箱名的字符邮箱名
                'password' => 'gambzfmrpidebcib',        //邮箱登陆密码 是授权码！！！！
            )
        ));
        $transport->setOptions($smtpOpt);
        $transport->send($msg);
        return true;
    }
}
