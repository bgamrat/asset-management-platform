<?php

Namespace App\Form\Admin\Schedule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityApp\Repository;
use App\Form\Common\DataTransformer\PersonToIdTransformer;

class EventRoleType extends AbstractType
{

    private $personToIdTransformer;

    public function __construct( PersonToIdTransformer $personToIdTransformer )
    {
        $this->personToIdTransformer = $personToIdTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'person', TextType::class, [
                    'required' => true,
                    'label' => false,
                    'mapped' => false
                ] )
                ->add( 'role', EntityType::class, [
                    'class' => 'App\Entity\Schedule\EventRoleType',
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
                ] )
                ->add( 'comment', TextType::class, [
                    'label' => false
                ] );
        $builder->get( 'person' )
                ->addModelTransformer( $this->personToIdTransformer );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Schedule\EventRole'
        ) );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'event_role';
    }

}
