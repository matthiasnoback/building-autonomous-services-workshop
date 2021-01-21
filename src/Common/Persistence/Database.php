<?php
declare(strict_types = 1);

namespace Common\Persistence;

use Assert\Assertion;
use NaiveSerializer\Serializer;
use RuntimeException;
use function Common\CommandLine\line;
use function Common\CommandLine\make_cyan;
use function Common\CommandLine\stdout;
use function get_class;
use function Safe\file_get_contents;
use function Safe\file_put_contents;

/**
 *
 */
final class Database
{
    private const ENV_DATABASE_DIRECTORY = 'DB_PATH';

    /**
     * @return void
     */
    public static function persist(IdentifiableObject $object) : void
    {
        self::doPersist(get_class($object), $object);
    }

    /**
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @param string $id
     * @return T
     */
    public static function retrieve(string $className, string $id)
    {
        return self::doRetrieve($className, $id);
    }

    /**
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @return array<T>
     */
    public static function retrieveAll(string $className): array
    {
        return self::doRetrieveAll($className);
    }

    /**
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @return void
     */
    public static function deleteAll(string $className) : void
    {
        self::doDeleteAll($className);
    }

    /**
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @param callable $filter
     * @return T|null
     */
    public static function findOne(string $className, callable $filter)
    {
        return self::doFindOne($className, $filter);
    }

    /**
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @param callable $filter
     * @return T[]
     */
    public static function find(string $className, callable $filter): array
    {
        return self::doFind($className, $filter);
    }

    /**
     * @template T of IdentifiableObject
     * @param class-string<T> $class
     * @return string The file path
     */
    private static function determineDatabaseFilePathFor(string $class) : string
    {
        $databaseFilePath =  self::databaseDirectory() . '/' . str_replace('\\', '_', $class) . '.json';

        Filesystem::ensureFilePathIsWritable($databaseFilePath);

        return $databaseFilePath;
    }

    /**
     * @return string
     */
    private static function databaseDirectory() : string
    {
        $databaseDirectory = getenv(self::ENV_DATABASE_DIRECTORY);
        Assertion::string($databaseDirectory, sprintf('Environment variable "%s" should be set', self::ENV_DATABASE_DIRECTORY));

        return $databaseDirectory;
    }

    /**
     * Provide an object that can be persisted (its `id()` method should return a string identifier. It will be serialized to disk.
     *
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @param T $object
     * @return void
     */
    private static function doPersist(string $className, $object): void
    {
        $id = (string)$object->id();

        $allData = self::loadAllObjects($className);
        $allData[$id] = $object;
        self::saveAllObjects($className, $allData);

        stdout(line(make_cyan('Persisted'), $className, ':', $id));
    }

    /**
     * Provide an id and you will retrieve the deserialized version of a previously persisted object.
     *
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @param string $id
     * @return T
     */
    private static function doRetrieve(string $className, string $id)
    {
        $data = self::doRetrieveAll($className);

        if (!array_key_exists($id, $data)) {
            throw new RuntimeException(sprintf('Unable to load object of type "%s" with ID "%s"', $className, $id));
        }

        $object = $data[$id];

        Assertion::isInstanceOf($object, $className);

        return $object;
    }

    /**
     * Load all previously persisted objects managed by this repository.
     *
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @return array<T>
     */
    private static function doRetrieveAll(string $className): array
    {
        return self::loadAllObjects($className);
    }

    /**
     * Load all previously persisted objects from disk.
     *
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @return array<T>
     */
    private static function loadAllObjects(string $className): array
    {
        $databaseFilePath = self::determineDatabaseFilePathFor($className);

        if (!file_exists($databaseFilePath)) {
            return [];
        }

        $objects = Serializer::deserialize("{$className}[]", file_get_contents($databaseFilePath));

        Assertion::allIsInstanceOf($objects, $className);

        return $objects;
    }

    /**
     * Save all objects in the given array to disk.
     *
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @param array<T> $allData
     * @return void
     * @throws RuntimeException
     */
    private static function saveAllObjects(string $className, array $allData): void
    {
        $databaseFilePath = self::determineDatabaseFilePathFor($className);

        file_put_contents($databaseFilePath, Serializer::serialize($allData));
    }

    /**
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @param callable $filter
     * @return array<T>
     */
    private static function doFind(string $className, callable $filter): array
    {
        return array_values(array_filter(self::loadAllObjects($className), $filter));
    }

    /**
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     * @param callable $filter
     * @return T|null
     */
    private static function doFindOne(string $className, callable $filter)
    {
        foreach (self::loadAllObjects($className) as $object) {
            if ($filter($object)) {
                return $object;
            }
        }

        return null;
    }


    /**
     * Delete all previously persisted objects managed by this repository.
     *
     * @template T of IdentifiableObject
     * @param class-string<T> $className
     */
    private static function doDeleteAll(string $className): void
    {
        $databaseFilePath = self::determineDatabaseFilePathFor($className);

        if (!is_file($databaseFilePath)) {
            return;
        }

        unlink($databaseFilePath);
    }
}
