<?php


namespace App\MessageHandler\Command;


use App\Message\Command\SendJoke;
use App\Message\Event\JokeReceivedEvent;
use App\Service\JokeService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SendJokeHandler implements MessageHandlerInterface
{
    /**
     * @var JokeService
     */
    private $jokeService;
    /**
     * @var MessageBusInterface
     */
    private $eventBus;

    /**
     * SendJokeHandler constructor.
     * @param JokeService $jokeService
     * @param MessageBusInterface $eventBus
     */
    public function __construct(JokeService $jokeService, MessageBusInterface $eventBus)
    {
        $this->jokeService = $jokeService;
        $this->eventBus = $eventBus;
    }

    public function __invoke(SendJoke $command)
    {
        $joke = $this->jokeService->getJokeByCategory($command->getCategory());
        $this->eventBus->dispatch(new JokeReceivedEvent($command->getEmail(), $joke));
    }

}
