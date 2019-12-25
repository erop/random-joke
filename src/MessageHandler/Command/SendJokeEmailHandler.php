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
     * @param string $jokeEmailSubjectTemplate
     */
    public function __construct(
        MailerInterface $mailer,
        ValidatorInterface $validator,
        string $jokeEmailSubjectTemplate
    ) {
        $this->mailer = $mailer;
        $this->subject = $jokeEmailSubjectTemplate;
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
            ->subject(sprintf($this->subject, $command->getCategory()))
            ->text($command->getJoke());
        $this->mailer->send($email);
    }


}
