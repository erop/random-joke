<?php


namespace App\MessageHandler\Command;


use App\Exception\Message\Command\SendJokeEmailException;
use App\Message\Command\SendJokeEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * SendJokeEmailHandler constructor.
     * @param MailerInterface $mailer
     * @param ValidatorInterface $validator
     * @param string $jokeEmailSubject
     */
    public function __construct(MailerInterface $mailer, ValidatorInterface $validator, string $jokeEmailSubject)
    {
        $this->mailer = $mailer;
        $this->subject = $jokeEmailSubject;
        $this->validator = $validator;
    }

    /**
     * @param SendJokeEmail $command
     * @throws TransportExceptionInterface
     */
    public function __invoke(SendJokeEmail $command)
    {
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new SendJokeEmailException((string)$errors);
        }
        $email = (new Email())
            ->from('admin@project.com')
            ->to($command->getEmail())
            ->subject($this->subject)
            ->text($command->getJoke());
        $this->mailer->send($email);
    }


}
