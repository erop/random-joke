<?php


namespace App\MessageHandler\Command;


use App\Exception\Message\Command\LogJokeException;
use App\Message\Command\LogJoke;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LogJokeHandler implements MessageHandlerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * LogJokeHandler constructor.
     * @param LoggerInterface $logger
     * @param ValidatorInterface $validator
     */
    public function __construct(LoggerInterface $logger, ValidatorInterface $validator)
    {
        $this->logger = $logger;
        $this->validator = $validator;
    }

    public function __invoke(LogJoke $command)
    {
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new LogJokeException((string )$errors);
        }
        $this->logger->info($command->getJoke());
    }

}
