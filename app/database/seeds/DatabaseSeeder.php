<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('BoardTableSeeder');
		$this->call('UserTableSeeder');
		$this->call('ApplyRecordTableSeeder');
	}

}