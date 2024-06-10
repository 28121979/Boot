<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'forfait',
            ])
            ->add('period', ChoiceType::class, [
                'choices' => [
                    'Matin' => 'morning',
                    'Après-midi' => 'afternoon',
                ],
                'required' => false,
                'placeholder' => 'Sélectionnez une période',
            ])
            ->add('isGroup', CheckboxType::class, [
                'label' => 'Est-ce une réservation de groupe?',
                'required' => false,
            ])
            ->add('participants', CollectionType::class, [
                'entry_type' => ParticipantType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'render_fieldset' => false,
            'show_legend' => false,
        ]);
    }
}
