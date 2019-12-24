<?php

namespace App\Form;

use App\Dto\SendJoke;
use App\Service\JokeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SendJokeType extends AbstractType
{
    /**
     * @var JokeService
     */
    private $jokeService;

    /**
     * SendJokeType constructor.
     * @param JokeService $jokeService
     */
    public function __construct(JokeService $jokeService)
    {
        $this->jokeService = $jokeService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add(
                'category',
                ChoiceType::class,
                [
                    'choices' => $this->buildCategoryOptions(),
                ]
            );
    }

    private function buildCategoryOptions(): array
    {
        $result = ['' => ''];
        $categories = $this->jokeService->getCategories();
        $values = array_values($categories);
        sort($values);
        foreach ($values as $value) {
            $result[ucfirst($value)] = $value;
        }
        return $result;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => SendJoke::class,
            ]
        );
    }
}
