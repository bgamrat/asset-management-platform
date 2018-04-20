<?php

Namespace App\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Admin\Asset\Type\IssueStatusType;

class IssueWorkflowType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'next', CollectionType::class, [
                    'entry_type' => IssueStatusType::class,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                    'allow_add' => false,
                    'allow_delete' => false,
                    'delete_empty' => false
                ] )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        return;
        $resolver->setDefaults( array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'statuses'
        ) );
    }

    public function getName()
    {
        return 'issue_statuses';
    }

}
