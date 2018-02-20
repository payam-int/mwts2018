<?php

namespace App\Form;

use App\Entity\SendArticle;
use App\Entity\SummaryArticle;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary', EntityType::class, [
                'class' => SummaryArticle::class,
                'query_builder' => $options['confirmed_query_builder']
            ])
            ->add('file', FileType::class, ['label' => 'Article as PDF']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('confirmed_query_builder');
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            'confirmed_query_builder' => null,
        ]);

    }
}
