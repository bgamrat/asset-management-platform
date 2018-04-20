<?php

Namespace App\Form\Admin\Asset;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class IssueNoteType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'updated_at', DateTimeType::class, [
                    'label' => 'common.updated',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd HH:mm:ss',
                    'required' => false,
                    'disabled' => true,
                ] )
                ->add( 'note', TextType::class, [
                    'label' => false
                ] )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Asset\IssueNote'
        ) );
    }

    public function getName()
    {
        return 'issue_note';
    }

}
