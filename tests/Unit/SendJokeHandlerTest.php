<?php

namespace App\Tests\Unit;

use App\Exception\Message\Command\SendJokeException;
use App\Message\Command\SendJoke;
use App\Message\Event\JokeReceivedEvent;
use App\MessageHandler\Command\SendJokeHandler;
use App\Service\JokeService;
use Generator;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SendJokeHandlerTest extends CustomTestCase
{
    /**
     * @param SendJoke $command
     * @param string $joke
     * @dataProvider getJokeCommands
     */
    public function testActionsOnHandlingSendJokeCommand(SendJoke $command, string $joke): void
    {
        $jokeService = $this->createMock(JokeService::class);
        $eventBus = $this->createMock(MessageBusInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn([]);
        $handler = new SendJokeHandler($jokeService, $eventBus, $validator);
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);

        $jokeService->expects($this->once())
            ->method('getJokeByCategory')
            ->with($command->getCategory())
            ->willReturn($joke);
        $event = new JokeReceivedEvent($command->getEmail(), $command->getCategory(), $joke);
        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));
        $handler($command);
    }

    public function getJokeCommands(): Generator
    {
        yield [new SendJoke('email1@example.com', 'category1'), 'Mwa-ha-ha!'];
        yield [new SendJoke('email2@example.com', 'category2'), 'Wow!'];
    }

    /**
     * @param SendJoke $command
     * @param int $errorsCount
     * @dataProvider getInvalidSendJokeCommands
     */
    public function testHandlingInvalidSendJokeCommand(SendJoke $command, int $errorsCount): void
    {
        $this->expectException(SendJokeException::class);
        $jokeService = $this->createMock(JokeService::class);
        $eventBus = $this->createMock(MessageBusInterface::class);
        $event = new JokeReceivedEvent($command->getEmail(), $command->getCategory(), '');
        $eventBus
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));
        $handler = new SendJokeHandler($jokeService, $eventBus, $this->getValidatorWithErrorsCount($errorsCount));
        $handler($command);
    }

    public function getInvalidSendJokeCommands(): Generator
    {
        yield [new SendJoke('', ''), 2];
        yield [new SendJoke('eropmail.ru', 'nerdy'), 2];
    }
}
