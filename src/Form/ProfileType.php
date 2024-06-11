<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prénom ne peut pas être vide.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
