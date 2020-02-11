<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
/**
 * @author Michael Hirschler <michael.vhirsch@gmail.com>
 *
 * @see https://en.wikipedia.org/wiki/ISO_9362#Structure
 */
class BicValidator extends \MailPoetVendor\Symfony\Component\Validator\ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, \MailPoetVendor\Symfony\Component\Validator\Constraint $constraint)
    {
        if (!$constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\Bic) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, __NAMESPACE__ . '\\Bic');
        }
        if (null === $value || '' === $value) {
            return;
        }
        $canonicalize = \str_replace(' ', '', $value);
        // the bic must be either 8 or 11 characters long
        if (!\in_array(\strlen($canonicalize), [8, 11])) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Bic::INVALID_LENGTH_ERROR)->addViolation();
            return;
        }
        // must contain alphanumeric values only
        if (!\ctype_alnum($canonicalize)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Bic::INVALID_CHARACTERS_ERROR)->addViolation();
            return;
        }
        // first 4 letters must be alphabetic (bank code)
        if (!\ctype_alpha(\substr($canonicalize, 0, 4))) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Bic::INVALID_BANK_CODE_ERROR)->addViolation();
            return;
        }
        // next 2 letters must be alphabetic (country code)
        if (!\ctype_alpha(\substr($canonicalize, 4, 2))) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Bic::INVALID_COUNTRY_CODE_ERROR)->addViolation();
            return;
        }
        // should contain uppercase characters only
        if (\strtoupper($canonicalize) !== $canonicalize) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Bic::INVALID_CASE_ERROR)->addViolation();
            return;
        }
    }
}
