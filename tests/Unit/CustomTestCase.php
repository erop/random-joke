<?php


namespace App\Tests\Unit;


use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomTestCase extends TestCase
{

    protected function getValidatorWithErrorsCount(int $errorsCount): ValidatorInterface
    {
        $errorList = $this->createMock(ConstraintViolationList::class);
        $errorList->method('count')->willReturn($errorsCount);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
        ->willReturn($errorList);
        return $validator;
    }
}
