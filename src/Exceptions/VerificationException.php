<?php
/**
 * Created by PhpStorm.
 * User: payam
 * Date: 2/17/18
 * Time: 8:29 PM
 */

namespace App\Exception;


use Symfony\Component\Security\Core\Exception\AuthenticationException;

class VerificationException extends AuthenticationException
{
    public function getMessageKey()
    {
        return 'Your account has not been verified.';
    }

}