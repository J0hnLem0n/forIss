<?php
/**
 * Created by PhpStorm.
 * User: enikolaev
 * Date: 06.07.2016
 * Time: 13:51
 */
namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array('label' => 'Username', 'required'=>false, 'attr'=> array('class' => 'form-control')))
            ->add('email', TextType::class, array('label' => 'email', 'required'=>false, 'attr'=> array('class' => 'form-control')))
            ->add('plain_password', 'password', array('label' => 'password', 'required'=>false, 'attr'=> array('class' => 'form-control')))
            ->add('save', SubmitType::class, array(
                'label' => 'Save',
                'attr'  => array('class' => 'btn btn-default'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
            'csrf_protection' => false,
        ));
    }
}