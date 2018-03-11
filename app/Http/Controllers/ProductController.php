<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use App\ProductImages;
use App\Category;
use Validator, Aut, DB;

class ProductController extends Controller
{
    public function allCategory (Request $request) {
      if ($request->has('status')) {
        $json['categories'] = Category::where('status', strtoupper($request->input('status')))->get();
        return response()->json($json, 200);
      }
      $json['categories'] = Category::all();
      return response()->json($json, 200);
    }

    public function getCategory ($id) {
      $json['category'] = $category = Category::find($id);
      if ($category) {
        $json['products'] = $category->products;
        return response()->json($json, 200);
      }
      return response()->json($json, 404);
    }

    public function deleteCategory ($catId) {
      $category = Category::find($catId);
      if (!$category) return response()->json(['error' => 'Category not found'], 400);
      $category->status = "INACTIVE";
      $category->save();
      $json['category'] = $category;
      return response()->json($json, 200);
    }

    public function updateCategory (Request $request) {
      $catId = $request->input('catId');
      if (!isset($catId)) return response()->json(['error' => 'Category not found'], 404);

      $validator = Validator::make($request->all(), [
          'name'             => 'required',
          'description'		   => 'required'
      ]);

      if ($validator->fails()) {
        $json['errors'] = $validator->messages();
        return response()->json($json, 400);
      }
      $category = Category::find($catId);
      $category->name = $request->input('name');
      $category->description = $request->input('description');
      if ($request->has('status')) {
        $category->status = strtoupper($request->input('status'));
      }
      $category->save();

      $json['category'] = $category;
      return response()->json($json, 200);
    }

    public function createCategory (Request $request) {
      $validator = Validator::make($request->all(), [
          'name'             => 'required',
          'description'		   => 'required'
      ]);

      if ($validator->fails()) {
        $json['errors'] = $validator->messages();
        return response()->json($json, 400);
      }
      $category = new Category;
      $category->name = $request->input('name');
      $category->description = $request->input('description');
      $category->save();

      $json['category'] = $category;
      return response()->json($json, 200);
    }

    public function allProducts (Request $request) {

      if ($request->input('status') === 'ALL' || !$request->has('status')) {
        $products = Product::where('status', 'ACTIVE')->orWhere('status', 'INACTIVE');
      } else {
        $products = Product::where('status', strtoupper($request->input('status')), 'AND');
      }

      if ($request->has('q')) {
        $products->where('brand', 'LIKE', '%'. $request->input('q') . '%')->where('specification', 'LIKE', '%'. $request->input('q') . '%', 'OR');
      }

      if ($request->has('categories')) {
        $products->whereHas('categories', function($q) use ($request){
          if (strpos($request->input('categories'), ",") !== false) {
            $cat = explode(",", $request->input('categories'));
            foreach($cat as $key => $value) {
              $q->orWhere('category_id', $value);
            }
          } else {
            $q->where('category_id', $request->input('categories'), 'OR');
          }
        });
      }

      // dd($products->toSql());
      $json['products'] = $this->returnProduct($products->get());
      return response()->json($json, 200);
    }

    public function returnProduct($products) {
      $array = [];
      foreach ($products as $key => $value) {
        $image = $value->images()->first();
        if ($image) {
          $path = $image->imagePath;
          $filename = $image->filename;
          $ext = $image->extension;
          $fsRef = $path . $filename . "." . $ext;
        } else {
          $fsRef = null;
        }

        $array[] = [
          'id'            => $value->id,
          'name'          => $value->name,
          'description'   => $value->description,
          'price'         => (float) $value->price,
          'brand'         => $value->brand,
          'specification' => $value->specification,
          'status'        => $value->status,
          'isFeatured'    => $value->isFeatured,
          'status'        => $value->status,
          'fsRef'         => $fsRef,
          'categories'    => $value->categories,
          'supplier'      => $value->supplier,
          'images'        => $value->images
        ];
      }

      return $array;
    }

    public function getProduct ($id) {
      $product = Product::find($id);
      if ($product) {
        $image = $product->images()->first();
        if ($image) {
          $path = $image->imagePath;
          $filename = $image->filename;
          $ext = $image->extension;
          $fsRef = $path . $filename . "." . $ext;
        } else {
          $fsRef = null;
        }

        $json['product'] = $array = [
          'id'            => $product->id,
          'name'          => $product->name,
          'description'   => $product->description,
          'price'         => (float) $product->price,
          'brand'         => $product->brand,
          'specification' => $product->specification,
          'status'        => $product->status,
          'isFeatured'    => $product->isFeatured,
          'status'        => $product->status,
          'fsRef'         => $fsRef,
          'categories'    => $product->categories,
          'supplier'      => $product->supplier,
          'images'        => $product->images
        ];
        return response()->json($json, 200);
      }
      return response()->json($json, 404);
    }

    public function updateStatus ($productId, $status) {
      $product = Product::find($id);
      if ($product) {
        $product->status = strtoupper($status);
        $product->save();

        $image = $product->images()->first();
        if ($image) {
          $path = $image->imagePath;
          $filename = $image->filename;
          $ext = $image->extension;
          $fsRef = $path . $filename . "." . $ext;
        } else {
          $fsRef = null;
        }

        $json['product'] = $array = [
          'id'            => $product->id,
          'name'          => $product->name,
          'description'   => $product->description,
          'price'         => (float) $product->price,
          'brand'         => $product->brand,
          'specification' => $product->specification,
          'status'        => $product->status,
          'isFeatured'    => $product->isFeatured,
          'status'        => $product->status,
          'fsRef'         => $fsRef,
          'categories'    => $product->categories,
          'supplier'      => $product->supplier,
          'images'        => $product->images
        ];
        return response()->json($json, 200);
      }
      return response()->json(['error', 'Product not found'], 404);
    }

    public function deleteProduct ($productId) {
      $product = Product::find($productId);
      if (!$product) return response()->json(['error' => 'Product not found'], 400);
      $product->status = "INACTIVE";
      $product->save();
      $json['product'] = $product;
      return response()->json($json, 200);
    }

    public function updateProduct (Request $request) {
      $prodId = $request->input('prodId');
      if (!isset($prodId)) return response()->json(['error' => 'Product not found'], 404);

      $validator = Validator::make($request->all(), [
          'name'       => 'required',
          'supplierId' => 'required'
      ]);

      if ($validator->fails()) {
        $json['errors'] = $validator->messages();
        return response()->json($json, 400);
      }
      $product = Product::find($prodId);
      $product->name        = $request->input('name');
      $product->description = $request->input('description');
      $product->price       = $request->input('price');
      $product->supplierId  = $request->input('supplierId');
      $product->brand       = $request->input('brand');
      $product->specification      = $request->input('specification');
      if ($request->has('status')) $product->status = strtoupper($request->input('status'));
      if ($request->has('isFeatured')) $product->isFeatured = $request->input('isFeatured');
      $product->save();

      $product->categories()->sync($request->input('categories'));

      $image = $product->images()->first();
      if ($image) {
        $path = $image->imagePath;
        $filename = $image->filename;
        $ext = $image->extension;
        $fsRef = $path . $filename . "." . $ext;
      } else {
        $fsRef = null;
      }

      $json['product'] = $array = [
        'id'            => $product->id,
        'name'          => $product->name,
        'description'   => $product->description,
        'price'         => (float) $product->price,
        'brand'         => $product->brand,
        'specification' => $product->specification,
        'status'        => $product->status,
        'isFeatured'    => $product->isFeatured,
        'status'        => $product->status,
        'fsRef'         => $fsRef,
        'categories'    => $product->categories,
        'supplier'      => $product->supplier,
        'images'        => $product->images
      ];
      return response()->json($json, 200);
    }

    public function createProduct (Request $request) {
      $validator = Validator::make($request->all(), [
          'name'       => 'required',
          'supplierId' => 'required'
      ]);

      if ($validator->fails()) {
        $json['errors'] = $validator->messages();
        return response()->json($json, 400);
      }
      $product = new Product;
      $product->name        = $request->input('name');
      $product->description = $request->input('description');
      $product->price       = $request->input('price') ? $request->input('price') : 0.00;
      $product->supplierId  = $request->input('supplierId');
      $product->brand       = $request->input('brand');
      $product->specification      = $request->input('specification');
      if ($request->has('status')) $product->status    = strtoupper($request->input('status'));
      if ($request->has('isFeatured')) $product->isFeatured = $request->input('isFeatured');
      $product->save();

      $product->categories()->sync($request->input('categories'));

      $image = $product->images()->first();
      if ($image) {
        $path = $image->imagePath;
        $filename = $image->filename;
        $ext = $image->extension;
        $fsRef = $path . $filename . "." . $ext;
      } else {
        $fsRef = null;
      }

      $json['product'] = $array = [
        'id'            => $product->id,
        'name'          => $product->name,
        'description'   => $product->description,
        'price'         => (float) $product->price,
        'brand'         => $product->brand,
        'specification' => $product->specification,
        'status'        => $product->status,
        'isFeatured'    => $product->isFeatured,
        'status'        => $product->status,
        'fsRef'         => $fsRef,
        'categories'    => $product->categories,
        'supplier'      => $product->supplier,
        'images'        => $product->images
      ];
      return response()->json($json, 200);
    }

    public function uploadProductImage(Request $request) {
      $validator = Validator::make($request->all(), [
          'productId'     => 'required',
          'productImage'	=> 'required|image'
      ]);

      if ($validator->fails()) {
        $json['errors'] = $validator->messages();
        return response()->json($json, 400);
      }

      $productId = $request->input('productId');
      $product = Product::find($productId);
      if (!$product) return response()->json(['error' => 'Product not found'], 400);

      $data = $request->all();
      if($image = array_pull($data, 'productImage')){
        $destinationPath = 'uploads/products/'.$product->id.'/';
        if (!file_exists(public_path($destinationPath))) {
         mkdir(public_path($destinationPath), 0777, true);
        }

        if($image->isValid()){
         $ext        = $image->getClientOriginalExtension();
         $filename   = $image->getFilename();
         $orig_name  = $image->getClientOriginalName();

         $for_upload = $filename . "." . $ext;
         $is_success = $image->move(public_path($destinationPath), $for_upload);

         if($is_success){
           $fileImage               = new ProductImages;
           $fileImage->productId    = $product->id;
           $fileImage->imagePath    = url('/') . "/" . $destinationPath;
           $fileImage->filename     = $filename;
           $fileImage->origFilename = $orig_name;
           $fileImage->extension    = $ext;
           $fileImage->save();
           $json['productImage'] = $fileImage;
           $json['product'] = $product;
           $json['productAssets'] = $product->images;
           return response()->json($json, 200);
         } else {
           return response()->json(['error' => 'Image upload failed.'], 400);
         }
        }
      }

      return response()->json(['error' => 'Image upload failed.'], 400);
    }

    public function deleteProductImage ($productId, Request $request) {
      dd($productId);
      dd($request->all());
    }
}
