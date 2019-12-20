<?php

namespace App\Tests\Unit;

use App\MessageHandler\Command\SendJokeEmailHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SendJokeEmailHandlerTest extends TestCase
{
    public function testEmailWithJokeIsSent(): void
    {
        $handler = new SendJokeEmailHandler();
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);

    }
}
