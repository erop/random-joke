<?php

namespace App\Tests\Unit;

use App\Exception\Message\Command\LogJokeException;
use App\Message\Command\LogJoke;
use App\MessageHandler\Command\LogJokeHandler;
use Generator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LogJokeHandlerTest extends CustomTestCase
{
    /**
     * @testdox Check the joke is logged properly on valid command
     */
    public function testJokeIsLogged(): void
    {
        $command = new LogJoke('email1@example.com', 'Mwa-ha-ha!');
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($command->getJoke());
        $handler = new LogJokeHandler($logger, $this->getValidatorWithErrorsCount(0));
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler($command);
    }

    public function getInvalidLogJokes(): Generator
    {
        yield [new LogJoke('email1@example.com', ''), 1];
        yield [new LogJoke('email2example.com', 'WFT!'), 1];
        yield [new LogJoke('email2example.com', ''), 2];
    }

    /**
     * @param LogJoke $command
     * @param int $errorsCount
     * @dataProvider getInvalidLogJokes
     */
    public function testExceptionIsThrownOnInvalidCommand(LogJoke $command, int $errorsCount): void
    {
        $this->expectException(LogJokeException::class);
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->method('info')
            ->with($command->getJoke());
        $handler = new LogJokeHandler($logger, $this->getValidatorWithErrorsCount($errorsCount));
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler($command);
    }


}
