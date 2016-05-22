<?php

namespace AppBundle\Form\Admin\User;

use AppBundle\Form\Common\PersonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    private $roles = null;

    public function __construct( Array $roles )
    {
        $this->roles = [];
        foreach( $roles as $n => $r )
        {
            $this->roles[$n] = new \stdClass();
            $this->roles[$n]->name = $n;
            $this->roles[$n]->value = $n;
        }
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'email', TextType::class, ['label' => 'common.email'] )
                ->add( 'username', TextType::class, ['label' => 'common.username', 'validation_groups' => array('registration')] )
                ->add( 'person', PersonType::class, array(
                    'data_class' => 'AppBundle\Entity\Person',
                    'by_reference' => true,
                    'required' => false,
                    'label' => false,
                    'empty_data' => null
                ) )
                ->add( 'enabled', CheckboxType::class, ['label' => 'common.enabled'] )
                ->add( 'locked', CheckboxType::class, ['label' => 'common.locked'] )
                ->add( 'groups', EntityType::class, [
                    'class' => 'AppBundle:Group',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'label' => 'common.groups',
                    'expanded' => true,
                    'choice_translation_domain' => false,
                    //'translation_domain' => false,
                    'attr' => array('data-type' => 'user-group-cb')
                ] )
                ->add( 'roles', ChoiceType::class, ['choices' => $this->roles,
                    'multiple' => true,
                    'choices_as_values' => true,
                    'expanded' => true,
                    'label' => 'common.roles',
                    'choice_label' => 'name',
                    'choice_translation_domain' => false,
                    //'translation_domain' => false,
                    'attr' => [ 'data-type' => 'user-role-cb']] );
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
