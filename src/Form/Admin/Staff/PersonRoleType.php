<?php

Namespace App\Form\Admin\Staff;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\ORM\EntityApp\Repository;

class PersonRoleType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'role', EntityType::class, [
                    'class' => 'App\Entity\Staff\Role',
                    'query_builder' => function (EntityApp\Repository $er)
                    {
                        return $er->createQueryBuilder( 'r' )
                                ->where( 'r.in_use = true' )
                                ->orderBy( 'r.name', 'ASC' );
                    },
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'common.role',
                    'choice_translation_domain' => false
                ] )
                ->add( 'start', DateType::class, [
                    'label' => 'common.start',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => false
                ] )
                ->add( 'end', DateType::class, [
                    'label' => 'common.end',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => false
                ] );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Staff\PersonRole'
        ) );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'person_role';
    }

}
