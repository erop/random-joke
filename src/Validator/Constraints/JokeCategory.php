<?php


namespace App\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class JokeCategory
 * @package App\Validator\Constraints
 * @Annotation
 */
class JokeCategory extends Constraint
{
    public $message = 'Provided joke category "{{category}}" is invalid';
}
