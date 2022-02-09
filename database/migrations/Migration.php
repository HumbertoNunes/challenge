<?php

namespace Database\Migrations;

use Database\Migrations\CreateUsersTable;
use Illuminate\Database\Schema\Builder;

class Migration
{
    private array $migrations;

    /**
     * Setup the migrations to run in the desired order
     */
    public function __construct()
    {
        $this->classes = [
            CreateUsersTable::class,
            CreateLogsTable::class
        ];
    }

    /**
     * Run all the migrations up
     *
     * @param Builder $builder
     *
     * @return void
     */
    public static function up(Builder $builder)
    {
        $migration = new static();

        foreach ($migration->classes as $class) {
            $migration->removeNamespace($class);

            echo "Migrating: {$migration->classname}" . PHP_EOL;
            $class::up($builder);
            echo "Migrated: {$migration->classname}" . PHP_EOL;
        }
    }

    /**
     * Run all the migrations down
     *
     * Builder $builder
     *
     * @return void
     */
    public static function down(Builder $builder)
    {
        $migration = new static();

        $migration->classes = array_reverse($migration->classes);

        foreach ($migration->classes as $class) {
            $migration->removeNamespace($class);

            echo "Migrating: {$migration->classname}" . PHP_EOL;
            $class::down($builder);
            echo "Migrated: {$migration->classname}" . PHP_EOL;
        }
    }

    /**
     * Remove the class namespace
     *
     * @param string $namespace
     *
     * @return voide
     */
    private function removeNamespace($class)
    {
        $exploded_namespace = explode('\\', $class);

        $this->classname = array_pop($exploded_namespace);
    }
}
