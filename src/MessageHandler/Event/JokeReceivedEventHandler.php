<?php


namespace App\MessageHandler\Event;


use App\Exception\Message\Event\JokeReceivedEventException;
use App\Message\Command\LogJoke;
use App\Message\Command\SendJokeEmail;
use App\Message\Event\JokeReceivedEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JokeReceivedEventHandler implements MessageHandlerInterface
{

    /**
     * @var MessageBusInterface
     */
    private $commandBus;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(MessageBusInterface $commandBus, ValidatorInterface $validator)
    {
        $this->commandBus = $commandBus;
        $this->validator = $validator;
    }

    /**
     * @param JokeReceivedEvent $event
     */
    public function __invoke(JokeReceivedEvent $event)
    {
        $errors = $this->validator->validate($event);
        if (count($errors) > 0) {
            throw new JokeReceivedEventException((string)$errors);
        }
        $email = $event->getEmail();
        $joke = $event->getJoke();
        $this->commandBus->dispatch(new SendJokeEmail($email, $joke));
        $this->commandBus->dispatch(new LogJoke($email, $joke));
    }


}
