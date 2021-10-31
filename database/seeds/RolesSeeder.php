<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'Super Admin',
                'description' => 'Super Admin can do anything over the application.',
                'level' => 1,
                'created_at' => Carbon::Now(),
                'updated_at' => Carbon::Now(),
            ], [
                'id' => 2,
                'name' => 'Admin',
                'description' => 'Admins can do anything over the application. Just cant access Super Admin and other Admins information.',
                'level' => 2,
                'created_at' => Carbon::Now(),
                'updated_at' => Carbon::Now(),
            ], [
                'id' => 3,
                'name' => 'Modaretor',
                'description' => 'Can access all information except the shop settings in under his/her shop.',
                'level' => 4,
                'created_at' => Carbon::Now(),
                'updated_at' => Carbon::Now(),
            ], [
                'id' => 4,
                'name' => 'Order Handler',
                'description' => 'Only can access order information under his/her shop.',
                'level' => 5,
                'created_at' => Carbon::Now(),
                'updated_at' => Carbon::Now(),
            ]
        ]);
    }
}
