<?php


namespace App\Dto;


class Joke
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $joke;

    /**
     * @var string[]|array
     */
    public $categories;
}
