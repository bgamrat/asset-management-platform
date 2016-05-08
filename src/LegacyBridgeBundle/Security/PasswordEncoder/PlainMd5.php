<?php

namespace LegacyBridgeBundle\Security\PasswordEncoder;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class PlainMd5 extends BasePasswordEncoder
{
    public function encodePassword($raw, $salt)
    {
        die(__FUNCTION__);
        if ($this->isPasswordTooLong($raw)) {
            throw new BadCredentialsException('Invalid password.');
        }

        return md5($raw);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        if ($this->isPasswordTooLong($raw)) {
            return false;
        }
        return $encoded === md5($raw);
    }
}
