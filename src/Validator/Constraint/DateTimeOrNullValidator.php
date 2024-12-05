<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateTimeOrNullValidator extends ConstraintValidator
{
    /**
     * Validate the value. The value is valid if it null or a DateTimeImmutable instance
     *
     * @param mixed $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {

        if (is_null($value) || is_a($value, \DateTimeImmutable::class)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}