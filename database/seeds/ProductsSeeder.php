<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductsSeeder extends Seeder
{

    private $itemCount = 30;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Product::class, $this->itemCount)->create();

        if (File::isDirectory(public_path('images/demo'))) {
            $products = \DB::table('products')->pluck('id')->toArray();
            $path = storage_path('app/public/'.image_storage_dir());

            if(!File::isDirectory($path))
                File::makeDirectory($path);

            $directories = glob(public_path('images/demo/products/*') , GLOB_ONLYDIR);

            foreach ($products as $item) {

                if(isset($directories[$item-1])){
                    $images = glob($directories[$item-1] . '/*.jpg');
                    foreach ($images as $key => $img) {
                        $img_name = str_random(10) . '.png';
                        File::copy($img,  "{$path}/{$img_name}");

                        DB::table('images')->insert([
                            [
                                'name' => $img_name,
                                'path' => image_storage_dir()."/{$img_name}",
                                'extension' => 'png',
                                'size' => 0,
                                'imageable_id' => $item,
                                'imageable_type' => 'App\Product',
                                'created_at' => Carbon::Now(),
                                'updated_at' => Carbon::Now(),
                            ]
                        ]);
                    }
                }
            }
        }
    }
}
