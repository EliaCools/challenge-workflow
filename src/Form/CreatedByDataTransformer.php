<?php


namespace App\Form;

// src/Form/DataTransformer/IssueToNumberTransformer.php


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CreatedByDataTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  User|null $value
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getId();
    }

    /**
     * Transforms a string (number) to an object (user).
     *
     * @param  string $value
     * @throws TransformationFailedException if object (user) is not found.
     */
    public function reverseTransform($value): ?User
    {
        // no issue number? It's optional, so that's ok
        if (!$value) {
            return null;
        }

        $user = $this->entityManager
            ->getRepository(User::class)
            // query for the issue with this id
            ->find($value)
        ;

        if (null === $user) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $value
            ));
        }

        return $user;
    }
}
