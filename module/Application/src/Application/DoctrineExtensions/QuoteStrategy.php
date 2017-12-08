<?php
namespace Application\DoctrineExtensions;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;

class QuoteStrategy extends \Doctrine\ORM\Mapping\DefaultQuoteStrategy
{
    /**
     * 1 ) Concatenate column name and counter
     * 2 ) Trim the column alias to the maximum identifier length of the platform.
     * If the alias is to long, characters are cut off from the beginning.
     * ) Strip non alphanumeric characters
     * ) Prefix with "_" if the result its numeric
     */
    public function getColumnAlias($columnName, $counter, AbstractPlatform $platform, ClassMetadata $class = null)
    {
        $columnName = $columnName . '_' . $counter;
        $columnName = substr($columnName, -$platform->getMaxIdentifierLength());
        $columnName = preg_replace('/[^A-Za-z0-9]/', '', $columnName);
        $columnName = is_numeric($columnName) ? '_' . $columnName : $columnName;

        return $platform->getSQLResultCasing($columnName);
    }
}
