<?php

namespace App\Form;

use App\Entity\Brand;
use App\Form\ModelType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BrandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control mb-3',
                    'autofocus' => true
                ],
                'label' => 'Nom de la marque :',
            ])
            ->add('models', CollectionType::class, [
                'label' => 'Modèles :',
                'entry_type' => ModelType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $brand = $event->getData();
                $models = $brand->getModels();
                foreach ($models as $model) {
                    $model->setBrand($brand);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Brand::class,
        ]);
    }
}
