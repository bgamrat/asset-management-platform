<?php

Namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityApp\Repository;
use App\Form\User\DataTransformer\InvitationToCodeTransformer;

class InvitationFormType extends AbstractType
{
    private $invitationTransformer;

    public function __construct(InvitationToCodeTransformer $invitationTransformer)
    {
        $this->invitationTransformer = $invitationTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->invitationTransformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'App\Entity\Invitation',
            'required' => true,
        ));
    }

    public function getParent()
    {
         return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }

    public function getName()
    {
        return 'app_invitation_type';
    }
}
