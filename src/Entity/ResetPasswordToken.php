<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResetPasswordTokenRepository")
 */
class ResetPasswordToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $hash;
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */

    private $expireTime;

    /**
     * ResetPasswordToken constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->hash = hash('md5', time() . $user->getEmail());
        $this->expireTime = new DateTime();
        $this->expireTime->add(new \DateInterval('PT12H'));
    }


    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
