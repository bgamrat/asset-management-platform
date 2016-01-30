<?php

namespace AppBundle\Form\Admin\User;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class UserType extends AbstractType
{

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        
        $builder
                ->add( 'username', TextType::class )
                ->add( 'email', TextType::class )
                ->add( 'enabled', CheckboxType::class )
                ->add( 'locked', CheckboxType::class )
                ->add( 'groups', EntityType::class, [
                    'class' => 'AppBundle:Group',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'choices_as_values' => true,
                    'expanded' => true
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
