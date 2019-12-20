<?php

namespace App\Tests\Unit;

use App\Message\Command\LogJoke;
use App\MessageHandler\Command\LogJokeHandler;
use Generator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LogJokeHandlerTest extends TestCase
{
    /**
     * @param LogJoke $command
     * @dataProvider getLogJokes
     */
    public function testJokeIsLogged(LogJoke $command): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($command->getJoke());
        $handler = new LogJokeHandler($logger);
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler($command);
    }

    public function getLogJokes(): Generator
    {
        yield [new LogJoke('email1@example.com', 'Mwa-ha-ha!')];
        yield [new LogJoke('email2@example.com', 'WFT!')];
    }
}
