<?php

 namespace AppBundle\Form;

 use Symfony\Component\Form\AbstractType;
 use Symfony\Component\Form\Extension\Core\Type\NumberType;
 use Symfony\Component\Form\FormBuilderInterface;
 use Symfony\Component\OptionsResolver\OptionsResolver;
 use Symfony\Component\Form\Extension\Core\Type\FileType;
 use Symfony\Component\Form\Extension\Core\Type\SubmitType;
 use Symfony\Component\Form\Extension\Core\Type\TextType;

 class ProductType extends AbstractType
 {
     public function buildForm(FormBuilderInterface $builder, array $options)
     {
         $builder
             ->add('thumbnail', FileType::class, array('label' => 'Миниатюра', 'required'=>false))
             ->add('name', TextType::class, array('label' => 'Наименование', 'attr'=> array('class' => 'form-control')))
             ->add('description', TextType::class, array('label' => 'Описание', 'attr'=> array('class' => 'form-control')))
             ->add('price', NumberType::class, array('label' => 'Цена', 'attr'=> array('class' => 'form-control')))
             ->add('save', SubmitType::class, array(
                    'label' => 'Save',
                    'attr'  => array('class' => 'btn btn-default'),
                 ))
         ;
     }

     public function configureOptions(OptionsResolver $resolver)
     {
         $resolver->setDefaults(array(
             'data_class' => 'AppBundle\Entity\Product',
             'csrf_protection' => false,
         ));
     }
 }