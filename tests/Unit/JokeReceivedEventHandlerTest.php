<?php

namespace App\Tests\Unit;

use App\Message\Command\LogJoke;
use App\Message\Command\SendJokeEmail;
use App\Message\Event\JokeReceivedEvent;
use App\MessageHandler\Event\JokeReceivedEventHandler;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class JokeReceivedEventHandlerTest extends TestCase
{
    /**
     * @param JokeReceivedEvent $event
     * @dataProvider getJokeReceivedEvents
     */
    public function testEventHandledProperly(JokeReceivedEvent $event): void
    {
        $emailCommand = new SendJokeEmail($event->getEmail(), $event->getJoke());
        $logCommand = new LogJoke($event->getEmail(), $event->getJoke());
        $commandBus = $this->createMock(MessageBusInterface::class);
        $commandBus->expects($this->at(0))
            ->method('dispatch')
            ->with($emailCommand)
            ->willReturn(new Envelope($emailCommand));
        $commandBus->expects($this->at(1))
            ->method('dispatch')
            ->with($logCommand)
            ->willReturn(new Envelope($emailCommand));
        $handler = new JokeReceivedEventHandler($commandBus);
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler($event);
    }

    public function getJokeReceivedEvents(): Generator
    {
        yield [new JokeReceivedEvent('email1@example.com', 'Mwa-ha-ha!')];
        yield [new JokeReceivedEvent('email2@example.com', 'WTF!')];
    }
}
