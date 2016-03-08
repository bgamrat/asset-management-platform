<?php

namespace AppBundle\Form\Admin\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    private $_roles = null;

    public function __construct( Array $roles )
    {
        $this->_roles = [];
        foreach( $roles as $n => $r )
        {
            $this->_roles[$n] = new \stdClass();
            $this->_roles[$n]->name = $n;
            $this->_roles[$n]->value = $n;
        }
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'email', TextType::class, ['label' => 'email'] )
                ->add( 'username', TextType::class, ['label' => 'username', 'validation_groups' => array('registration')] )
                ->add( 'enabled', CheckboxType::class, ['label' => 'enabled'] )
                ->add( 'locked', CheckboxType::class, ['label' => 'locked'] )
                ->add( 'roles', ChoiceType::class, ['choices' => $this->_roles,
                    'multiple' => true,
                    'choices_as_values' => true,
                    'expanded' => true,
                    'label' => 'roles',
                    'choice_label' => 'name',
                    'attr' => [ 'data-type' => 'user-role-cb']] );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\User'
        ) );
    }

    public function getName()
    {
        return 'user';
    }

}
