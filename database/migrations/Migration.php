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
		$this->migrations = [
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
		$instance = new static();

		foreach($instance->migrations as $migration) {
			$instance->getClassBase($migration);

			echo "Migrating: {$instance->migration_class}" . PHP_EOL; 
			$migration::up($builder);
			echo "Migrated: {$instance->migration_class}" . PHP_EOL;
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
		$instance = new static();

		foreach($instance->migrations as $migration) {
			$instance->getClassBase($migration);

			echo "Migrating: {$instance->migration_class}" . PHP_EOL;
			$migration::down($builder);
			echo "Migrated: {$instance->migration_class}" . PHP_EOL;
		}
	}

	/**
	 * Retrieve the class basename of the migration class
	 *
	 * @param string $migration
	 *
	 * @return voide
	 */
	private function getClassBase($migration)
	{
		$exploded_namespace = explode('\\', $migration);

		$this->migration_class = array_pop($exploded_namespace);
	}
}