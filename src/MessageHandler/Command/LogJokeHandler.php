<?php


namespace App\MessageHandler\Command;


use App\Message\Command\LogJoke;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LogJokeHandler implements MessageHandlerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LogJokeHandler constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(LogJoke $command)
    {
        $this->logger->info($command->getJoke());
    }

}
