<?php


namespace App\Message\Command;


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
     */
    private $category;

    /**
     * SendJoke constructor.
     * @param $email
     * @param $category
     */
    public function __construct(string $email, string $category)
    {
        $this->email = $email;
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }


}
