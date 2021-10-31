<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SystemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('systems')->insert([
            // 'maintenance_mode' => 1,
            'install_verion' => \App\System::VERSION,
            'name' => 'oneCart',
            'legal_name' => 'oneCart Inc.',
            'email' => 'notify@demo.com',
            'support_email' => 'support@demo.com',
            'timezone_id' => '35',
            'currency_id' => 148,
            'length_unit' => 'cm',
            'weight_unit' => 'gm',
            'valume_unit' => 'liter',
            'show_currency_symbol' => 1,
            'show_space_after_symbol' => 0,
            'google_analytic_report' => 0,

            // Social media
            'facebook_link' => 'https://www.facebook.com/',
            'twitter_link' => 'https://twitter.com/',
            'google_plus_link' => 'https://plus.google.com/',
            'pinterest_link' => 'https://www.pinterest.com/',
            'instagram_link' => 'https://www.instagram.com/',
            'youtube_link' => 'https://www.youtube.com/',

            // Address Defults
            'address_show_map' => 1,
            'address_default_country' => 840, //Country id
            'address_default_state' => 1221, //State id
            'address_show_country' => 1,

            // Manual payments
            'cod_additional_details' => 'Our man will collect the payment when deliver the item to your doorstep.',
            'cod_payment_instructions' => 'Payment instructions for COD',
            'wire_additional_details' => 'Send the payment via Bank Wire Transfer.',
            'wire_payment_instructions' => 'Payment instructions for Bank Wire Transfer',

            'created_at' => Carbon::Now(),
            'updated_at' => Carbon::Now(),
        ]);

        DB::table('addresses')->insert([
            'address_type' => 'Primary',
            'address_line_1' => 'Platform Address',
            'state_id' => 806,
            'zip_code' => 63585,
            'country_id' => 604,
            'city' => 'Hollywood',
            'addressable_id' => 1,
            'addressable_type' => 'App\System',
            'created_at' => Carbon::Now(),
            'updated_at' => Carbon::Now(),
        ]);
    }
}
