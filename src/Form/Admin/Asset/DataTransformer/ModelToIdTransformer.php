<?php

Namespace App\Form\Admin\Asset\DataTransformer;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ModelToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (model) to a string (id).
     *
     * @param  Issue|null $model
     * @return string
     */
    public function transform($model)
    {
        if (null === $model) {
            return '';
        }

        return $model->getId();
    }

    /**
     * Transforms a string (id) to an object (model).
     *
     * @param  string $modelId
     * @return Issue|null
     * @throws TransformationFailedException if object (model) is not found.
     */
    public function reverseTransform($modelId)
    {
        // no model id? It's optional, so that's ok
        if (!$modelId) {
            return;
        }

        $model = $this->em
            ->getRepository('App\Entity\Asset\Model')
            ->find($modelId)
        ;

        if (null === $model) {
            throw new TransformationFailedException(sprintf(
                'An model with id "%s" does not exist!',
                $modelId
            ));
        }
        return $model;
    }
}