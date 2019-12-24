<?php

namespace App\Controller;

use App\Dto\SendJoke as SendJokeDto;
use App\Form\SendJokeType;
use App\Message\Command\SendJoke as SendJokeCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * DefaultController constructor.
     * @param ValidatorInterface $validator
     * @param MessageBusInterface $messageBus
     */
    public function __construct(ValidatorInterface $validator, MessageBusInterface $messageBus)
    {
        $this->validator = $validator;
        $this->messageBus = $messageBus;
    }

    /**
     * @Route("/", name="default")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $dto = new SendJokeDto();
        $form = $this->createForm(SendJokeType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SendJokeDto $dto */
            $dto = $form->getData();
            $command = new SendJokeCommand($dto->getEmail(), $dto->getCategory());
            $this->messageBus->dispatch($command);
        }
        return $this->render('default/index.html.twig', ['form' => $form->createView()]);
    }
}
