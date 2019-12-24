<?php


namespace App\Dto;


use App\Validator\Constraints\JokeCategory;
use Symfony\Component\Validator\Constraints as Assert;

class SendJoke
{
    /**
     * @var string
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     * @JokeCategory()
     */
    private $category;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }


}
