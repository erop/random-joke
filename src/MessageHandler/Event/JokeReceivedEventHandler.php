<?php


namespace App\MessageHandler\Event;


use App\Message\Command\LogJoke;
use App\Message\Command\SendJokeEmail;
use App\Message\Event\JokeReceivedEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class JokeReceivedEventHandler implements MessageHandlerInterface
{

    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(JokeReceivedEvent $event)
    {
        $this->commandBus->dispatch(new SendJokeEmail($event->getEmail(), $event->getJoke()));
        $this->commandBus->dispatch(new LogJoke($event->getEmail(), $event->getJoke()));
    }


}
