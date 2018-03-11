<?php

use Illuminate\Database\Seeder;
use App\Supplier;
use App\Category;
use App\Product;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cat1 = new Category;
        $cat1->name = 'Multiparameter Photometers';
        $cat1->description = '';
        $cat1->save();

        $cat2 = new Category;
        $cat2->name = 'Single Parameter Photometers';
        $cat2->description = '';
        $cat2->save();

        $cat3 = new Category;
        $cat3->name = 'Sensor Technology';
        $cat3->description = '';
        $cat3->save();

        $cat4 = new Category;
        $cat4->name = 'Electrochemical Meters';
        $cat4->description = '';
        $cat4->save();

        $sup1 = new Supplier;
        $sup1->name = 'TBS';
        $sup1->address = 'Makati';
        $sup1->currency = 'PHP';
        $sup1->save();

        $forCat1 = ['Photometer 8000', 'Photometer 7500', 'Photometer 7100'];
        foreach ($forCat1 as $key => $value) {
          $product = new Product;
          $product->name = $value;
          $product->description = '';
          $product->price = 0.00;
          $product->supplierId = $sup1->id;
          $product->save();
          $product->categories()->sync($cat1->id);
        }

        $forCat2 = ['Compact Turbimeter', 'Compact Chlorometer', 'Compact Chlorometer Duo', 'Compact ClO2+ Meter', 'Compact Ozone Meter', 'Compact Ammonia + Meter', 'Compact Ammonia Duo Meter'];
        foreach ($forCat2 as $key => $value) {
          $product = new Product;
          $product->name = $value;
          $product->description = '';
          $product->price = 0.00;
          $product->supplierId = $sup1->id;
          $product->save();
          $product->categories()->sync($cat2->id);
        }

        $forCat3 = ['ChlordioX Plus', 'ChlordioXense', 'ChloroSense', 'Scanning Analyzer'];
        foreach ($forCat3 as $key => $value) {
          $product = new Product;
          $product->name = $value;
          $product->description = '';
          $product->price = 0.00;
          $product->supplierId = $sup1->id;
          $product->save();
          $product->categories()->sync($cat3->id);
        }

        $forCat4 = ['pH', 'Conductivity / TDS', 'Dissolved Oxygen', 'Multiparameter'];
        foreach ($forCat4 as $key => $value) {
          $product = new Product;
          $product->name = $value;
          $product->description = '';
          $product->price = 0.00;
          $product->supplierId = $sup1->id;
          $product->save();
          $product->categories()->sync($cat4->id);
        }
    }
}
