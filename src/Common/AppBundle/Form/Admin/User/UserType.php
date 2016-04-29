<?php

namespace Common\AppBundle\Form\Admin\User;

use Common\AppBundle\Form\Common\PersonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                ->add( 'email', TextType::class, ['label' => 'common.email'] )
                ->add( 'username', TextType::class, ['label' => 'common.username', 'validation_groups' => array('registration')] )
                ->add( 'person', CollectionType::class, array(
                    'entry_type' => PersonType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true,
                    'required' => false,
                    'label' => false
                ) )
                ->add( 'enabled', CheckboxType::class, ['label' => 'common.enabled'] )
                ->add( 'locked', CheckboxType::class, ['label' => 'common.locked'] )
                ->add( 'groups', EntityType::class, [
                    'class' => 'AppBundle:Group',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'choices_as_values' => true,
                    'label' => 'common.groups',
                    'expanded' => true,
                    'attr' => array('data-type' => 'user-group-cb')
                ] )
                ->add( 'roles', ChoiceType::class, ['choices' => $this->_roles,
                    'multiple' => true,
                    'choices_as_values' => true,
                    'expanded' => true,
                    'label' => 'common.roles',
                    'choice_label' => 'name',
                    'attr' => [ 'data-type' => 'user-role-cb']] );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'groups' => [],
            'data_class' => 'Common\AppBundle\Entity\User'
        ) );
    }

    public function getName()
    {
        return 'user';
    }

}
