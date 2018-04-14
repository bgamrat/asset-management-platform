<?php

Namespace App\Form\Admin\Asset\DataTransformer;

use Entity\Asset\Barcode;
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
    public function reverseTransform( $assetId )
    {
        // no barcode id? It's optional, so that's ok
        if( !$assetId )
        {
            return;
        }

        $asset = $this->em->getRepository( 'Entity\Asset\Asset' )->find( $assetId );

        if( null === $asset )
        {
            throw new TransformationFailedException( sprintf(
                    'An asset with asset id "%s" does not exist!', $assetId
            ) );
        }
        return $asset;
    }

}
