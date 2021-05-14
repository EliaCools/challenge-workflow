<?php


namespace App\Form\DataTransformer;


use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TicketDataTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @inheritDoc
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getId();
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value): ?Ticket
    {
        // no issue number? It's optional, so that's ok
        if (!$value) {
            return null;
        }

        $ticket = $this->entityManager
            ->getRepository(Ticket::class)
            // query for the issue with this id
            ->find($value)
        ;

        if (null === $ticket) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $value
            ));
        }

        return $ticket;
    }
}