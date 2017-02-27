<?php

namespace AppBundle\Form\Admin\Asset\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Asset\IssueStatus;

class IssueStatusType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( [
            'class' => 'AppBundle\Entity\Asset\IssueStatus',
            'multiple' => true,
            'expanded' => true,
            'required' => false,
            'label' => false,
            'preferred_choices' => function($status, $key, $index)
            {
                return $status->isDefault();
            },
            'choice_translation_domain' => false,
            'choice_label' => 'status'
        ] );
    }

    public function getParent()
    {
        return EntityType::class;
    }

}
