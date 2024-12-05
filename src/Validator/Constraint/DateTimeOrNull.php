<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class DateTimeOrNull extends Constraint
{
    public string $message = 'The value "{{ value }}" is not null or a DateTimeImmutable instance.';
}