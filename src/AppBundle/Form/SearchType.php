<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Наименование', 'required'=>false, 'attr'=> array('class' => 'form-control')))
            ->add('isSorting', ChoiceType::class, array(
                'attr' => array('class' => 'form-control'),
                'choices'  => array(
                    'name' => 'name',
                    'description' => 'description',
                    'price' => 'price',
                )))
            ->add('save', SubmitType::class, array(
                'label' => 'Filter',
                'attr'  => array('class' => 'btn btn-default'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }
}