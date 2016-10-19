<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use AppBundle\Entity\Asset\Barcode;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BarcodeToEntityTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms ArrayCollection to a string (id).
     *
     * @param  Issue|null $barcode
     * @return string
     */
    public function transform($barcodes)
    {
        //dump($barcodes);die;  
        return null;
    }

    /**
     * Transforms a CollectionType to an ArrayCollection
     *
     * @param  string $barcodeBarcode
     * @return Issue|null
     * @throws TransformationFailedException if object (barcode) is not found.
     */
    public function reverseTransform($barcodes)
    {
        // no barcode id? It's optional, so that's ok
        if (!$barcodes) {
            return;
        }

//dump($barcodes);die;       
        /*
        
        $barcode = $this->em
            ->getRepository('AppBundle:Barcode')
            ->findBy(['barcode' => $barcodeBarcode])
        ;

        if (null === $barcode) {
            throw new TransformationFailedException(sprintf(
                'Barcode "%s" does not exist!',
                $barcodeBarcode
            ));
        }
        return $barcode;
         * 
         */
        return false;
    }
}