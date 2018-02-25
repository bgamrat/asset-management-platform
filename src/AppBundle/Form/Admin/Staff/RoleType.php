<?php

namespace AppBundle\Form\Admin\Staff;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class RoleType extends AbstractType
{
        private $authorizationChecker;
    private $roles = null;

    public function __construct( AuthorizationChecker $authorizationChecker, Array $roles)
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

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class )
                ->add( 'name', TextType::class )
                ->add( 'comment', TextType::class )
                ->add( 'in_use', CheckboxType::class )
        ;
        if( $this->authorizationChecker->isGranted( 'ROLE_ADMIN_USER_ADMIN' ) )
        {
            $builder
                    ->add( 'roles', ChoiceType::class, ['choices' => $this->roles,
                        'multiple' => true,
                        'expanded' => true,
                        'label' => 'common.roles',
                        'choice_label' => 'name',
                        'choice_translation_domain' => 'user',
                        'attr' => [ 'data-type' => 'role-role-cb']] );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( ['label' => false,
            'data_class' => 'AppBundle\Entity\Staff\Role'
        ] );
    }

    public function getName()
    {
        return 'role';
    }

}
