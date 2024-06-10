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
                'delete_label' => 'Effacer l\'avatar',
                'download_label' => 'Télécharger',
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
                'label' => 'Avatar',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrez votre nom'],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrez votre prénom'],
            ])
            ->add('isCompany', CheckboxType::class, [
                'label' => 'Est-ce une entreprise ?',
                'required' => false,
                'attr' => ['id' => 'isCompanyCheckbox'],
            ])
            ->add('companyName', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'required' => false,
                'attr' => [
                    'id' => 'companyNameField',
                    'placeholder' => 'Entrez le nom de l\'entreprise',
                ],
            ])
            ->add('siretNumber', TextType::class, [
                'label' => 'Numéro SIRET',
                'required' => false,
                'attr' => [
                    'id' => 'siretNumberField',
                    'placeholder' => 'Entrez le numéro SIRET',
                ],
            ])
            ->add('billingAddress', TextType::class, [
                'label' => 'Adresse de facturation',
                'attr' => ['placeholder' => 'Entrez l\'adresse de facturation'],
            ])
            ->add('billingCity', TextType::class, [
                'label' => 'Ville de facturation',
                'attr' => ['placeholder' => 'Entrez la ville de facturation'],
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['placeholder' => 'Entrez le code postal'],
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => 'Entrez le numéro de téléphone'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
            'translation_domain' => 'forms', // Utilisation d'un domaine de traduction si nécessaire
        ]);
    }
}
