<?php


namespace App\Form;

use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StatusDataTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (status) to a string (number).
     *
     * @param  Status|null $status
     */
    public function transform($status): string
    {
        if (null === $status) {
            return '';
        }

        return $status->getId();
    }

    /**
     * Transforms a string (number) to an object (status).
     *
     * @param  string $statusNumber
     * @throws TransformationFailedException if object (status) is not found.
     */
    public function reverseTransform($statusNumber): ?Status
    {
        // no issue number? It's optional, so that's ok
        if (!$statusNumber) {
            return null;
        }

        $status = $this->entityManager
            ->getRepository(Status::class)
            // query for the issue with this id
            ->find($statusNumber)
        ;

        if (null === $status) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $statusNumber
            ));
        }

        return $status;
    }
}
