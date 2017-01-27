<?php

namespace AppBundle\Form\Admin\Client\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CategoryToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (category) to a string (id).
     *
     * @param  Issue|null $category
     * @return string
     */
    public function transform($category)
    {
        if (null === $category) {
            return '';
        }

        return $category->getName();
    }

    /**
     * Transforms a string (id) to an object (category).
     *
     * @param  string $categoryId
     * @return Issue|null
     * @throws TransformationFailedException if object (category) is not found.
     */
    public function reverseTransform($categoryId)
    {
        // no category id? It's optional, so that's ok
        if (!$categoryId) {
            return;
        }

        $category = $this->em
            ->getRepository('AppBundle\Entity\Asset\Category')
            ->find($categoryId)
        ;

        if (null === $category) {
            throw new TransformationFailedException(sprintf(
                'An category with id "%s" does not exist!',
                $categoryId
            ));
        }
        return $category;
    }
}