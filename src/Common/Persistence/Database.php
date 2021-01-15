<?php
declare(strict_types = 1);

namespace Common\Persistence;

use Assert\Assertion;

final class Database
{
    private const ENV_DATABASE_DIRECTORY = 'DB_PATH';

    /**
     * @see Repository::persist()
     *
     * @param IdentifiableObject $object
     * @return void
     */
    public static function persist(IdentifiableObject $object) : void
    {
        self::repositoryFor(\get_class($object))->persist($object);
    }

    /**
     * @see Repository::retrieve()
     *
     * @param string $className
     * @param string $id
     * @return object
     */
    public static function retrieve(string $className, string $id)
    {
        return self::repositoryFor($className)->retrieve($id);
    }

    /**
     * @see Repository::retrieveAll()
     *
     * @param string $className
     * @return array
     */
    public static function retrieveAll(string $className): array
    {
        return self::repositoryFor($className)->retrieveAll();
    }

    /**
     * @see Repository::deleteAll()
     *
     * @param string $className
     * @return void
     */
    public static function deleteAll(string $className) : void
    {
        self::repositoryFor($className)->deleteAll();
    }

    /**
     * @param string $className
     * @param callable $filter
     * @return object|null
     */
    public static function findOne(string $className, callable $filter)
    {
        return self::repositoryFor($className)->findOne($filter);
    }

    /**
     * @param string $className
     * @param callable $filter
     * @return object[]|array
     */
    public static function find(string $className, callable $filter): array
    {
        return self::repositoryFor($className)->find($filter);
    }

    /**
     * @param string $className
     * @return Repository
     */
    private static function repositoryFor(string $className) : Repository
    {
        return new Repository($className, self::determineDabaseFilePathFor($className));
    }

    /**
     * @param string $class
     * @return string The file path
     */
    private static function determineDabaseFilePathFor(string $class) : string
    {
        return self::databaseDirectory() . '/' . str_replace('\\', '_', $class) . '.json';
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
}
