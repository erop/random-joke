<?php


namespace App\Message\Command;


use Symfony\Component\Validator\Constraints as Assert;

class LogJoke
{
    /**
     * @var string
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $joke;

    /**
     * LogJoke constructor.
     * @param string $email
     * @param string $joke
     */
    public function __construct(string $email, string $joke)
    {
        $this->email = $email;
        $this->joke = $joke;
    }

    /**
     * @return string
     */
    public function getJoke(): string
    {
        return $this->joke;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

}
