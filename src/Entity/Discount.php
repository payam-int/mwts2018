<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DiscountRepository")
 */
class Discount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="articles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $used = false;

    /**
     * @ORM\Column(type="string")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Payment", inversedBy="discounts")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */

    private $payment;

    /**
     * Discount constructor.
     * @param $user
     * @param $price
     * @param $payment
     */
    public function __construct($user, $price, $payment)
    {
        $this->user = $user;
        $this->price = $price;
        $this->payment = $payment;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * @return mixed
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * @param mixed $used
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function __toString()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param mixed $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }




}
