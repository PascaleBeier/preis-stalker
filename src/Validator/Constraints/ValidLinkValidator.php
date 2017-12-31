<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidLinkValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $url = parse_url($value, PHP_URL_HOST);
        if (strpos($url, 'www.amazon') === false) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ link }}', $value)
                ->addViolation();
        }
    }

}