<?php


namespace App\Message\Command;


use App\Validator\Constraints\JokeCategory;
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
     * @var string
     * @JokeCategory()
     */
    private $category;

    /**
     * LogJoke constructor.
     * @param string $email
     * @param string $category
     * @param string $joke
     */
    public function __construct(string $email, string $category, string $joke)
    {
        $this->email = $email;
        $this->joke = $joke;
        $this->category = $category;
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

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

}
