<?php

Namespace App\Form\Admin\Staff;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\ORM\EntityRepository;

class PersonEmploymentStatusType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder->add( 'employment_status', EntityType::class, [
                    'class' => 'Entity\Staff\EmploymentStatus',
                    'query_builder' => function (EntityRepository $er)
                    {
                        return $er->createQueryBuilder( 'es' )
                                ->where( 'es.in_use = true' )
                                ->orderBy( 'es.name', 'ASC' );
                    },
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'label' => 'staff.employment_status',
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
            'data_class' => 'Entity\Staff\PersonEmploymentStatus'
        ) );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'person_employment_status';
    }

}
