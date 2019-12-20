<?php


namespace App\MessageHandler\Command;


use App\Message\Command\SendJokeEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class SendJokeEmailHandler implements MessageHandlerInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var string
     */
    private $subject;

    /**
     * SendJokeEmailHandler constructor.
     * @param MailerInterface $mailer
     * @param string $jokeEmailSubject
     */
    public function __construct(MailerInterface $mailer, string $jokeEmailSubject)
    {
        $this->mailer = $mailer;
        $this->subject = $jokeEmailSubject;
    }

    /**
     * @param SendJokeEmail $command
     * @throws TransportExceptionInterface
     */
    public function __invoke(SendJokeEmail $command)
    {
        $email = (new Email())
            ->to($command->getEmail())
            ->subject($this->subject)
            ->text($command->getJoke());
        $this->mailer->send($email);
    }


}
