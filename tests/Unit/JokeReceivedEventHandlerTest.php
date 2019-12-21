<?php

namespace App\Tests\Unit;

use App\Exception\Message\Event\JokeReceivedEventException;
use App\Message\Command\LogJoke;
use App\Message\Command\SendJokeEmail;
use App\Message\Event\JokeReceivedEvent;
use App\MessageHandler\Event\JokeReceivedEventHandler;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JokeReceivedEventHandlerTest extends TestCase
{

    public function testEventHandledSuccessfully(): void
    {
        $event = new JokeReceivedEvent('email1@example.com', 'Mwa-ha-ha!');
        $emailCommand = $this->getSendJokeEmailCommand($event);
        $logCommand = $this->getLogJokeCommand($event);
        $commandBus = $this->createMock(MessageBusInterface::class);
        $commandBus->expects($this->at(0))
            ->method('dispatch')
            ->with($emailCommand)
            ->willReturn(new Envelope($emailCommand));
        $commandBus->expects($this->at(1))
            ->method('dispatch')
            ->with($logCommand)
            ->willReturn(new Envelope($emailCommand));
        $handler = new JokeReceivedEventHandler($commandBus, $this->getValidatorWithErrorsCount(0));
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler($event);
    }

    /**
     * @param JokeReceivedEvent $event
     * @return SendJokeEmail
     */
    public function getSendJokeEmailCommand(JokeReceivedEvent $event): SendJokeEmail
    {
        return new SendJokeEmail($event->getEmail(), $event->getJoke());
    }

    /**
     * @param JokeReceivedEvent $event
     * @return LogJoke
     */
    public function getLogJokeCommand(JokeReceivedEvent $event): LogJoke
    {
        return new LogJoke($event->getEmail(), $event->getJoke());
    }

    private function getValidatorWithErrorsCount(int $errorsCount): ValidatorInterface
    {
        $errorList = $this->createMock(ConstraintViolationList::class);
        $errorList->method('count')->willReturn($errorsCount);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn($errorList);
        return $validator;
    }

    public function getInvalidJokeReceivedEvents(): Generator
    {
        yield [new JokeReceivedEvent('email2@example.com', ''), 1];
        yield [new JokeReceivedEvent('email2example.com', 'WTF!'), 1];
        yield [new JokeReceivedEvent('email2example.com', ''), 2];
    }

    /**
     * @param JokeReceivedEvent $event
     * @param int $errorsCount
     * @dataProvider getInvalidJokeReceivedEvents
     */
    public function testInvalidEventRaisesException(JokeReceivedEvent $event, int $errorsCount): void
    {
        $this->expectException(JokeReceivedEventException::class);
        $commandBus = $this->createMock(MessageBusInterface::class);
        $handler = new JokeReceivedEventHandler($commandBus, $this->getValidatorWithErrorsCount($errorsCount));
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler($event);
    }

}
