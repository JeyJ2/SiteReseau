<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Titre'])
            ->add('country',TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Pays'])
            ->add('city', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Ville'])
            ->add('address', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Adresse'])
            ->add('date', DateType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Date'])
            ->add('capacity', NumberType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Capacité'])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Description'])
            ->add('picture', FileType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Image', 'mapped' => false, 'required' => false])
            ->add('submit', SubmitType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
