<?php


namespace App\Validator\Constraints;


use App\Service\JokeService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class JokeCategoryValidator extends ConstraintValidator
{
    /**
     * @var JokeService
     */
    private $service;

    /**
     * JokeCategoryValidator constructor.
     * @param JokeService $service
     */
    public function __construct(JokeService $service)
    {
        $this->service = $service;
    }


    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ( ! $constraint instanceof JokeCategory) {
            throw new UnexpectedTypeException($constraint, JokeCategory::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if ( ! is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if ( ! in_array($value, $this->service->getCategories(), true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{category}}', $value)
                ->addViolation();
        }
    }
}
