<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatarFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Effacer',
                'download_label' => 'Téléchager',
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
            ])
            ->add('name')
            ->add('firstname')
            ->add('isCompany', CheckboxType::class, [
                'label' => 'Est-ce une entreprise ?',
                'required' => false,
                'attr' => ['id' => 'isCompanyCheckbox'],
            ])
            ->add('companyName', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'required' => false,
                'attr' => ['id' => 'companyNameField'],
            ])
            ->add('siretNumber', TextType::class, [
                'label' => 'Numéro SIRET',
                'required' => false,
                'attr' => ['id' => 'siretNumberField'],
            ])
            ->add('billingAddress')
            ->add('billingCity')
            ->add('zipCode')
            ->add('phoneNumber');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
