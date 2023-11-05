<?php

namespace App\Form;

use App\Entity\UploadedFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UploadedFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filenam')
            ->add('originalFilename')
            ->add('MimeType')
            ->add('size')
            ->add('path')
            ->add('creatAt')
            ->add('updateAt')
            ->add('file', FileType::class, [
                'label' => 'Choose file',
                
                // unmapped means that this field is not associated with any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the file
                // whenever you edit the details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            // You can add more MIME types here
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UploadedFile::class,
        ]);
    }
}
