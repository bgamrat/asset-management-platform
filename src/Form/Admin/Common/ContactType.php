<?php

Namespace App\Form\Admin\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use App\Form\Admin\Common\DataTransformer\ContactToIdTransformer;
use App\Form\Admin\Common\DataTransformer\ContactTypeToIdTransformer;
use App\Form\Common\DataTransformer\PersonToIdTransformer;
use App\Form\Common\DataTransformer\AddressToIdTransformer;
use App\Form\Admin\EventListener\ContactFieldSubscriber;

class ContactType extends AbstractType
{

    private $em,$entities;

    public function __construct( EntityManager $em, Array $entities )
    {
        $this->em = $em;
        $this->entities = $entities;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
                ->add( 'id', HiddenType::class )
                ->add( 'contact_entity_id', HiddenType::class, [ 'property_path' => 'entity'] )
                ->add( 'contact_type', HiddenType::class, ['property_path' => 'type'] )
                ->add( 'person_id', HiddenType::class, [ 'property_path' => 'person'] )
                ->add( 'name', HiddenType::class )
                ->add( 'address_id', HiddenType::class, [ 'required' => false, 'property_path' => 'address'] )
        ;
        $builder->get( 'contact_type' )
                ->addModelTransformer( new ContactTypeToIdTransformer( $this->em ) );
        $builder->get( 'person_id' )
                ->addModelTransformer( new PersonToIdTransformer( $this->em ) );
        $builder->get( 'address_id' )
                ->addModelTransformer( new AddressToIdTransformer( $this->em ) );
        $builder->addEventSubscriber( new ContactFieldSubscriber($this->em, $this->entities) );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'App\Entity\Common\Contact',
            'allow_extra_fields' => true
        ) );
    }

    public function getName()
    {
        return 'contact';
    }

}
