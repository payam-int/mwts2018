<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @var
     * @ORM\Column(type="string", nullable=TRUE)
     */
    private $referenceId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $price;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $online_payment_price;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="payments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $state = 0;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $metadata = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $done = false;


    /**
     * @ORM\OneToMany(targetEntity="Discount", mappedBy="payment")
     */
    private $discounts;

    /**
     * Payment constructor.
     * @param string $price
     * @param $user
     */
    public function __construct($price, $user)
    {
        $this->price = $price;
        $this->online_payment_price = $price;
        $this->user = $user;
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
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * @param mixed $referenceId
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(string $price)
    {
        $this->price = $price;
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
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState(int $state)
    {
        $this->state = $state;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return mixed
     */
    public function getOnlinePaymentPrice()
    {
        return $this->online_payment_price;
    }

    /**
     * @param mixed $online_payment_price
     */
    public function setOnlinePaymentPrice($online_payment_price)
    {
        $this->online_payment_price = $online_payment_price;
    }

    /**
     * @return mixed
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param mixed $discounts
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;
    }

    public function calcAmount()
    {
        return $this->getPrice();
    }

    /**
     * @return mixed
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * @param mixed $done
     */
    public function setDone($done)
    {
        $this->done = $done;
    }




}
