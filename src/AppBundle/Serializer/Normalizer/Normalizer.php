<?php

namespace AppBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

/**
 * Description of Default
 *
 * @author bgamrat
 */
class Normalizer extends SerializerAwareNormalizer implements NormalizerInterface
{

    /**
     * {@inheritdoc}
     */
    public function normalize( $object, $format = null, array $context = array() )
    {
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler( function ($object)
        {
            if (method_exists($object,'getName')) {
                return $object->getName();
            } else {
                if (method_exists($object,'getType')) {
                    return $object->getType();
                } else {
                    if (method_exists($object,'getStatus')) {
                        return $object->getStatus();
                    }
                }
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
