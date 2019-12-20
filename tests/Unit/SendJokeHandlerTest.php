<?php

namespace App\Tests\Unit;

use App\Message\Command\SendJoke;
use App\Message\Event\JokeReceivedEvent;
use App\MessageHandler\Command\SendJokeHandler;
use App\Service\JokeService;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SendJokeHandlerTest extends TestCase
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
        $handler = new SendJokeHandler($jokeService, $eventBus);
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);

        $jokeService->expects($this->once())
            ->method('getJokeByCategory')
            ->with($command->getCategory())
            ->willReturn($joke);
        $event = new JokeReceivedEvent($command->getEmail(), $joke);
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
}
