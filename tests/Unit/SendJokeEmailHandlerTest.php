<?php

namespace App\Tests\Unit;

use App\Message\Command\SendJokeEmail;
use App\MessageHandler\Command\SendJokeEmailHandler;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class SendJokeEmailHandlerTest extends TestCase
{
    /**
     * @param SendJokeEmail $command
     * @dataProvider getSendJokeEmails
     * @throws TransportExceptionInterface
     */
    public function testEmailWithJokeIsSent(SendJokeEmail $command): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $subject = 'Joke Email Subject';
        $email = (new Email())
            ->to($command->getEmail())
            ->subject($subject)
            ->text($command->getJoke());
        $mailer->expects($this->once())
            ->method('send')
            ->with($email);
        $handler = new SendJokeEmailHandler($mailer, $subject);
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler($command);
    }

    public function getSendJokeEmails(): Generator
    {
        yield [new SendJokeEmail('erop1@example.com', 'Mwa-ha-ha!')];
        yield [new SendJokeEmail('erop2@example.com', 'WTF!')];
    }
}
