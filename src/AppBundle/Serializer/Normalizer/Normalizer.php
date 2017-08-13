<?php

namespace AppBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Description of Default
 *
 * @author bgamrat
 *
 * Thanks to: https://stackoverflow.com/questions/43569313/symfony3-serializing-nested-entities/43569381s
 */
class Normalizer implements NormalizerInterface, NormalizerAwareInterface
{

    use NormalizerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function normalize( $object, $format = null, array $context = array() )
    {
        $encoder = new JsonEncoder();

        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler( function ($object)
        {
            if( method_exists( $object, 'getName' ) )
            {
                return $object->getName();
            }
            if( method_exists( $object, 'getType' ) )
            {
                return $object->getType();
            }
            if( method_exists( $object, 'getStatus' ) )
            {
                return $object->getStatus();
            }
        } );

        $serializer = new Serializer( array($normalizer), array($encoder) );

        return $serializer->normalize( $object );
    }

    public function supportsNormalization( $data, $format = NULL )
    {
        return ($format === 'json');
    }

}
