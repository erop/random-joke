<?php


namespace App\MessageHandler\Command;


use App\Exception\Message\Command\SendJokeException;
use App\Message\Command\SendJoke;
use App\Message\Event\JokeReceivedEvent;
use App\Service\JokeService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * SendJokeHandler constructor.
     * @param JokeService $jokeService
     * @param MessageBusInterface $eventBus
     * @param ValidatorInterface $validator
     */
    public function __construct(JokeService $jokeService, MessageBusInterface $eventBus, ValidatorInterface $validator)
    {
        $this->jokeService = $jokeService;
        $this->eventBus = $eventBus;
        $this->validator = $validator;
    }

    public function __invoke(SendJoke $command)
    {
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new SendJokeException((string)$errors);
        }
        $category = $command->getCategory();
        $joke = $this->jokeService->getJokeByCategory($category);
        $this->eventBus->dispatch(new JokeReceivedEvent($command->getEmail(), $category, $joke));
    }

}
