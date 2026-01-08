<?php

declare(strict_types=1);

namespace JMS\JobQueueBundle\Entity\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class SafeObjectType extends Type
{
    public const TYPE_NAME =  'jms_job_safe_object';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBlobTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return serialize($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = is_resource($value) ? stream_get_contents($value) : $value;

        try {
            $result = @unserialize($value);
        } catch (\Throwable $e) {
            throw new ConversionException(sprintf('Failed to convert database value to %s via unserialize: %s', $this->getName(), $e->getMessage()), 0, $e);
        }

        // unserialize() returns false both on failure and when successfully unserializing boolean false ('b:0;')
        // We need to distinguish between these cases to avoid false positives on valid false values
        if ($result === false && $value !== 'b:0;') {
            throw new ConversionException(sprintf('Failed to convert database value to %s via unserialize.', $this->getName()));
        }

        return $result;
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
