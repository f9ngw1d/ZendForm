<?php

namespace Manage\Form;

use Zend\Session\Container;
use Zend\Validator\AbstractValidator;
use Zend\Session\Storage\SessionArrayStorage;

class ImgCaptchaValidator extends AbstractValidator
{
    public function isValid($value)
    {
        // TODO: Implement isValid() method.
        $this->setValue($value);

        $isValid = true;

        $sessionKey_Capthca = 'sesscaptcha';
        $containerCaptcha = new Container('captcha');
        $captchaWord = $containerCaptcha->item;
        // $sessionStorage = new SessionArrayStorage();
        // //获取session临时保存的验证码
        // $captchaWord = $sessionStorage->offsetGet($sessionKey_Capthca);
        //比较输入的验证码和生成的验证码
        if ($captchaWord != $value) {
            $isValid = false;
        }
        return $isValid;
    }
}