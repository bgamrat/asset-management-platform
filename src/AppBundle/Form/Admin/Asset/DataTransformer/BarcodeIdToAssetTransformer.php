<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use AppBundle\Entity\Asset\Barcode;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BarcodeIdToAssetTransformer implements DataTransformerInterface
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    /**
     * Transforms ArrayCollection to a string (id).
     *
     * @param  Issue|null $barcode
     * @return string
     */
    public function transform( $asset )
    {
        if( $asset === null )
        {
            return null;
        }
        
        $barcodes = $asset->getBarcodes();
        foreach ($barcodes as $b) {
            if ($b->isActive()) {
                return $b->getId();
            }
        }

        return null;
    }

    /**
     * Transforms a CollectionType to an ArrayCollection
     *
     * @param  string $barcodeBarcode
     * @return Issue|null
     * @throws TransformationFailedException if object (barcode) is not found.
     */
    public function reverseTransform( $barcodeId )
    {
        // no barcode id? It's optional, so that's ok
        if( !$barcodeId )
        {
            return;
        }

        $asset = $this->em->getRepository( 'AppBundle\Entity\Asset\Asset' )->findOneByBarcodeId( $barcodeId );

        if( null === $asset )
        {
            throw new TransformationFailedException( sprintf(
                    'An asset with barcode id "%s" does not exist!', $barcodeId
            ) );
        }
        return $asset;
    }

}
