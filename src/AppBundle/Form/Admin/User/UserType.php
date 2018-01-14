<?php

namespace AppBundle\Form\Admin\User;

use AppBundle\Form\Common\Type\PersonType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    private $authorizationChecker;
    private $roles = null;

    public function __construct( AuthorizationCheckerInterface $authorizationChecker, Array $roles )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->roles = [];
        $role = null;
        foreach( $roles as $n => $r )
        {
            if( $n === 'ROLE_API' )
            {
                continue;
            }
            $role = new \stdClass();
            $role->name = $n;
            $role->value = $n;
            $this->roles[] = $role;
        }
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class, ['label' => false] )
                ->add( 'email', TextType::class, ['label' => 'common.email'] )
                ->add( 'username', TextType::class, ['label' => 'common.username', 'validation_groups' => array('registration')] )
                ->add( 'person', PersonType::class, [
                    'required' => true,
                    'label' => false
                ] );
        if( $this->authorizationChecker->isGranted( 'ROLE_ADMIN_USER_ADMIN' ) )
        {
            $builder
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
                        'attr' => [ 'data-type' => 'user-group-cb']
                    ] )
                    ->add( 'roles', ChoiceType::class, ['choices' => $this->roles,
                        'multiple' => true,
                        'expanded' => true,
                        'label' => 'common.roles',
                        'choice_label' => 'name',
                        'choice_translation_domain' => false,
                        //'translation_domain' => false,
                        'attr' => [ 'data-type' => 'user-role-cb']] );
        }
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
