<?php

namespace App\Tests\Unit;

use App\Exception\Message\Command\SendJokeEmailException;
use App\Message\Command\SendJokeEmail;
use App\MessageHandler\Command\SendJokeEmailHandler;
use Generator;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class SendJokeEmailHandlerTest extends CustomTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testEmailWithJokeIsSent(): void
    {
        $command = new SendJokeEmail('erop1@example.com', 'nerdy', 'Mwa-ha-ha!');
        $mailer = $this->createMock(MailerInterface::class);
        $subject = 'Joke Email Subject';
        $email = (new Email())
            ->from('admin@project.com')
            ->to($command->getEmail())
            ->subject($subject)
            ->text($command->getJoke());
        $mailer->expects($this->once())
            ->method('send')
            ->with($email);
        $handler = new SendJokeEmailHandler($mailer, $this->getValidatorWithErrorsCount(0), $subject);
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler($command);
    }

    public function getInvalidSendJokeEmails(): Generator
    {
        yield [new SendJokeEmail('erop1@example.com', 'nerdy', ''), 1];
        yield [new SendJokeEmail('erop2example.com', 'nerdy', 'WTF!'), 1];
        yield [new SendJokeEmail('', '', ''), 3];
    }

    /**
     * @dataProvider getInvalidSendJokeEmails
     * @param SendJokeEmail $command
     * @param int $errorsCount
     * @throws TransportExceptionInterface
     */
    public function testExceptionIsThrownOnInvalidCommand(SendJokeEmail $command, int $errorsCount): void
    {
        $this->expectException(SendJokeEmailException::class);
        $mailer = $this->createMock(MailerInterface::class);
        $subject = 'Joke Email Subject';
        $email = $this->createStub(Email::class);
        $mailer
            ->method('send')
            ->with($email);
        $handler = new SendJokeEmailHandler($mailer, $this->getValidatorWithErrorsCount($errorsCount), $subject);
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler($command);
    }


}
