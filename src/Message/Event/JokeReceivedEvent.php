<?php


namespace App\Message\Event;


class JokeReceivedEvent
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $joke;

    /**
     * JokeReceivedEvent constructor.
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
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getJoke(): string
    {
        return $this->joke;
    }

}
