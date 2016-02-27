<?php

namespace AppBundle\Form\Admin\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{

    public function buildForm( FormBuilderInterface $builder, array $options )
    {

        $builder
                ->add( 'email', TextType::class,['label' => 'email'] )
                ->add( 'username', TextType::class, ['label' => 'username','validation_groups' => array('registration')] )
                ->add( 'enabled', CheckboxType::class,['label' => 'enabled'] )
                ->add( 'locked', CheckboxType::class,['label' => 'locked'] )
                ->add( 'groups', EntityType::class, [
                    'class' => 'AppBundle:Group',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'choices_as_values' => true,
                    'expanded' => true,
                    'attr' => array('data-type' => 'user-group-cb'),
                    'label' => 'groups'
                ] );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'groups' => [],
            'data_class' => 'AppBundle\Entity\User'
            
        ) );
    }

    public function getName()
    {
        return 'user';
    }

}
