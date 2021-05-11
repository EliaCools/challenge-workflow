<?php


namespace App\Form;

use DateTime;
use Exception;
use Symfony\Component\Form\DataTransformerInterface;

class DateDataTransformer implements DataTransformerInterface
{

    public function __construct()
    {
    }

    /**
     * @param DateTime|null $dateCreated
     * @return string
     */
    public function transform($dateCreated)
    {
        if (null === $dateCreated) {
            return '';
        }
        return $dateCreated->format('Y-m-d H:i:s');
    }

    /**
     * @param string $createDateString
     * @return DateTime
     * @throws Exception
     */
    public function reverseTransform($createDateString)
    {
        $datetime = new DateTime($createDateString);
        return $datetime;
    }
}
