<?php

namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class CardTypeEnumType extends Type
{
    const CARD_TYPE_ENUM = 'cardtypeenum';

    const CREDIT = 'credit';
    const DEBIT = 'debit';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('credit', 'debit')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value;
    }

    public function getName()
    {
        return self::CARD_TYPE_ENUM;
    }
}