<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidLink extends Constraint
{
    public $message = 'Der Link {{ link }} ist kein gültiger Amazon-Produktlink';

}