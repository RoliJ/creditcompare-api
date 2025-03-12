<?php

namespace App\Helper;

use Doctrine\ORM\EntityManagerInterface;

class EntityHelper
{
/**
     * Get the entity class name from the table name.
     *
     * @param EntityManagerInterface $em Entity manager
     * @param string $table Table name
     * @return string Entity class name, null if not found
     */
    public static function getClassNameFromTableName(EntityManagerInterface $em, string $table): ?string
    {
        $classNames = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        foreach ($classNames as $className) {
            $classMetaData = $em->getClassMetadata($className);
            if ($table == $classMetaData->getTableName()) {
                return $classMetaData->getName();
            }
        }
        return null;
    }

    /**
     * Get the field name from the column name.
     *
     * @param EntityManagerInterface $em Entity manager
     * @param string $className Entity class name
     * @param string $column Column name
     * @return string Field name, null if not found
     */
    public static function getFieldNameFromColumnName(EntityManagerInterface $em, string $className, string $column): ?string
    {
        $classMetaData = $em->getClassMetadata($className);
        if ($classMetaData) {
            return $classMetaData->getFieldForColumn($column);
        }
        return null;
    }
}