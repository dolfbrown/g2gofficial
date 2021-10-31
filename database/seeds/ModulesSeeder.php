<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ModulesSeeder extends Seeder
{
    /**
     * All modules and its attributes.
     * This will generate the role accesses and will be used on permission control.
     * access = Common, Admin, and Super Admin
     * actions is the comma separated string that will be use for permission control
     *
     * @var arr
     */
    private $modules = [
        // Module name  => Access level //
        'Appearance' => [
            'access' => 'Admin',
            'actions' => 'customize'
        ],

        'Attribute' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Carrier' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Category' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Category Group' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Category Sub Group' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Coupon' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Cart' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Customer' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Dispute' => [
            'access' => 'Common',
            'actions' => 'view,response'
        ],

        'Email Template' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Gift Card' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Manufacturer' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Message' => [
            'access' => 'Common',
            'actions' => 'view,add,update,delete,reply'
        ],

        'Module' => [
            'access' => 'Super Admin',
            'actions' => 'view,add,edit,delete'
        ],

        'Order' => [
            'access' => 'Common',
            'actions' => 'view,add,fulfill,archive'
        ],

        'Packaging' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Product' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Refund' => [
            'access' => 'Common',
            'actions' => 'view,initiate,update,approve'
        ],

        'Role' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Supplier' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Shipping Zone' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Shipping Rate' => [
            'access' => 'Common',
            'actions' => 'add,edit,delete'
        ],

        'System' => [
            'access' => 'Super Admin',
            'actions' => 'view,edit'
        ],

        'System Config' => [
            'access' => 'Admin',
            'actions' => 'view,edit'
        ],

        'Tax' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'Warehouse' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

        'User' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete,login'
        ],

        'Utility' => [
            'access' => 'Common',
            'actions' => 'view,add,edit,delete'
        ],

    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->modules as $name => $attributes)
        {
            DB::table('modules')->insert(
                [
                    'name' => $name,
                    'description' => 'Manage all '.strtolower($name).'.',
                    'access' => $attributes['access'],
                    'actions' => $attributes['actions'],
                    'created_at' => Carbon::Now(),
                    'updated_at' => Carbon::Now(),
                ]
            );
        }
    }

}
