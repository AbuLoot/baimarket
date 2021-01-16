<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Image;
use Storage;

use App\Mode;
use App\Option;
use App\Region;
use App\Comment;
use App\Company;
use App\Product;
use App\ProductLang;
use App\Category;
use App\Language;
use App\Currency;
use App\ImageTrait;

class ProductController extends Controller
{
    use ImageTrait;

    public function index()
    {
        $user = Auth::user();
        $ids = $user->companies()->pluck('id');
        $products = Product::whereIn('company_id', $ids)->orderBy('updated_at')->get();

        return view('ads.index', ['user' => $user, 'products' => $products]);
    }

    public function create($lang)
    {
        $currency = Currency::where('lang', (($lang == 'ru') ? 'kz' : $lang))->first();
        $categories = Category::get()->toTree();
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $options = Option::orderBy('sort_id')->get();
        $modes = Mode::all();

        return view('ads.create', ['modes' => $modes, 'currency' => $currency, 'categories' => $categories, 'options' => $options]);
    }

    public function store(Request $request, $lang)
    {
        $this->validate($request, [
            'title' => 'required|min:2|unique:products_lang',
            'company_id' => 'required|numeric',
            // 'barcode' => 'required',
            // 'images' => 'mimes:jpeg,jpg,png,svg,svgs,bmp,gif',
        ]);

        $company = Auth::user()->companies()->where('id', $request->company_id)->first();
        $dirName = $company->id.'/'.time();
        $introImage = NULL;
        $images = [];

        Storage::makeDirectory('img/products/'.$dirName);

        if ($request->hasFile('images')) {
            $images = $this->saveImages($request, $dirName);
            $introImage = current($images)['present_image'];
        }

        $product = new Product;
        if (isset($request->latitude) && isset($request->longitude)) {
            $product->latitude = $request->latitude;
            $product->longitude = $request->longitude;
        }
        $product->sort_id = ($request->sort_id > 0) ? $request->sort_id : $product->count() + 1;
        $product->company_id = $request->company_id;
        $product->category_id = $request->category_id;
        $product->region_id = $request->region_id;
        // $product->barcode = $request->barcode;
        $product->count = ($request->count > 0) ? $request->count : 1;
        $product->condition = $request->condition;
        $product->area = $request->area;
        $product->time = $request->time;
        $product->phones = $request->phones;
        $product->image = $introImage;
        $product->images = serialize($images);
        $product->path = $dirName;
        $product->mode = (isset($request->mode)) ? $request->mode : 0;
        $product->status = $request->status;
        $product->save();

        if ( ! is_null($request->modes_id)) {
            $product->modes()->attach($request->modes_id);
        }

        if ( ! is_null($request->options_id)) {
            $product->options()->attach($request->options_id);
        }

        $product_lang = new ProductLang;
        $product_lang->product_id = $product->id;
        $product_lang->slug = str_slug($request->title);
        $product_lang->title = $request->title;
        $product_lang->title_extra = $request->title_extra;
        $product_lang->meta_title = ($request->meta_title) ? $request->meta_title : $request->title;
        $product_lang->meta_description = ($request->meta_description) ? $request->meta_description : $request->title;
        $product_lang->price = $request->price;
        $product_lang->description = $request->description;
        $product_lang->characteristic = $request->characteristic;
        $product_lang->lang = $request->lang;
        $product_lang->save();

        return redirect($lang.'/my-ads')->with('status', 'Товар добавлен!');
    }

    public function edit($lang, $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::get()->toTree();
        $product_lang = ProductLang::where('product_id', $product->id)->where('lang', $lang)->first();
        $currency = Currency::where('lang', (($lang == 'ru') ? 'kz' : $lang))->first();
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $options = Option::orderBy('sort_id')->get();
        $grouped = $options->groupBy('data');
        $modes = Mode::all();

        return view('ads.edit', ['modes' => $modes, 'product' => $product, 'product_lang' => $product_lang, 'currency' => $currency, 'categories' => $categories, 'options' => $options, 'grouped' => $grouped]);
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2',
            'company_id' => 'required|numeric',
            // 'barcode' => 'required',
        ]);

        $product = Product::findOrFail($id);
        $product_lang = ProductLang::where('product_id', $product->id)->where('lang', $lang)->first();

        if ($product_lang == NULL) {
            $product_lang = new ProductLang;
        }

        $dirName = $product->path;
        $images = unserialize($product->images);

        // Remove images
        if (isset($request->remove_images)) {
            $images = $this->removeImages($request, $images, $product);
            $introImage = (isset($images[0]['present_image'])) ? $images[0]['present_image'] : 'no-image-middle.png';
        }

        // Adding new images
        if ($request->hasFile('images')) {

            if ( ! file_exists('img/products/'.$product->company_id) OR empty($product->path)) {
                $dirName = $product->company_id.'/'.time();
                Storage::makeDirectory('img/products/'.$dirName);
                $product->path = $dirName;
            }

            $images = $this->uploadImages($request, $dirName, $images, $product);
            $introImage = current($images)['present_image'];
        }

        // Change directory for new category
        if ($product->company_id != $request->company_id AND file_exists('img/products/'.$product->path)) {
            $dirName = $request->company_id.'/'.time();
            Storage::move('img/products/'.$product->path, 'img/products/'.$dirName);
            $product->path = $dirName;
        }

        // Adding map
        if (isset($request->latitude) && isset($request->longitude)) {
            $product->latitude = $request->latitude;
            $product->longitude = $request->longitude;
        }

        $product->sort_id = ($request->sort_id > 0) ? $request->sort_id : $product->count() + 1;
        $product->company_id = $request->company_id;
        $product->category_id = $request->category_id;
        $product->region_id = $request->region_id;
        // $product->barcode = $request->barcode;
        $product->count = ($request->count > 0) ? $request->count : 1;
        $product->condition = $request->condition;
        $product->area = $request->area;
        $product->time = $request->time;
        $product->phones = $request->phones;
        if (isset($introImage)) $product->image = $introImage;
        $product->images = serialize($images);
        $product->path = $dirName;
        $product->mode = (isset($request->mode)) ? $request->mode : 0;
        $product->status = $request->status;
        $product->save();

        $product->modes()->sync($request->modes_id);

        $product->options()->sync($request->options_id);

        $product_lang->product_id = $product->id;
        $product_lang->slug = str_slug($request->title);
        $product_lang->title = $request->title;
        $product_lang->title_extra = $request->title_extra;
        $product_lang->meta_title = ($request->meta_title) ? $request->meta_title : $request->title;
        $product_lang->meta_description = ($request->meta_description) ? $request->meta_description : $request->title;
        $product_lang->price = $request->price;
        $product_lang->description = $request->description;
        $product_lang->characteristic = $request->characteristic;
        $product_lang->lang = $request->lang;
        $product_lang->save();

        return redirect($lang.'/my-ads')->with('status', 'Товар обновлен!');
    }

    public function saveImages($request, $dirName)
    {
        $order = 1;
        $images = [];

        foreach ($request->file('images') as $key => $image)
        {
            $imageName = 'image-'.$order.'-'.str_slug($request->title).'.'.$image->getClientOriginalExtension();

            // $watermark = Image::make('img/watermark.png');

            // Creating present images
            $this->resizeOptimalImage($image, 450, 300, '/img/products/'.$dirName.'/present-'.$imageName, 90);

            // Storing original images
            // $image->storeAs('/img/products/'.$dirName, $imageName);
            $this->resizeOptimalImage($image, 800, 460, '/img/products/'.$dirName.'/'.$imageName, 90);

            $images[$key]['image'] = $imageName;
            $images[$key]['present_image'] = 'present-'.$imageName;
            $order++;
        }

        return $images;
    }

    public function uploadImages($request, $dirName, $images = [], $product)
    {
        $order = (!empty($images)) ? count($images) : 1;
        $order = time() + 1;

        foreach ($request->file('images') as $key => $image)
        {
            $imageName = 'image-'.$order.'-'.str_slug($request->title).'.'.$image->getClientOriginalExtension();

            // $watermark = Image::make('img/watermark.png');

            // Creating present images
            $this->resizeOptimalImage($image, 450, 300, '/img/products/'.$dirName.'/present-'.$imageName, 90);

            // Storing original images
            $this->resizeOptimalImage($image, 800, 460, '/img/products/'.$dirName.'/'.$imageName, 90);

            if (isset($images[$key])) {

                Storage::delete([
                    'img/products/'.$product->path.'/'.$images[$key]['image'],
                    'img/products/'.$product->path.'/'.$images[$key]['present_image']
                ]);
            }

            $images[$key]['image'] = $imageName;
            $images[$key]['present_image'] = 'present-'.$imageName;
            $order++;
        }

        ksort($images);
        return $images;
    }

    public function removeImages($request, $images = [], $product)
    {
        foreach ($request->remove_images as $kvalue) {

            if (!isset($request->images[$kvalue])) {

                Storage::delete([
                    'img/products/'.$product->path.'/'.$images[$kvalue]['image'],
                    'img/products/'.$product->path.'/'.$images[$kvalue]['present_image']
                ]);

                unset($images[$kvalue]);
            }
        }

        ksort($images);
        return $images;
    }

    public function destroy($lang, $id)
    {
        $product = Product::findOrFail($id);

        if (! empty($product->images)) {

            $images = unserialize($product->images);

            foreach ($images as $image) {

                if ($image['image'] != 'no-image-middle.png') {
                    Storage::delete([
                        'img/products/'.$product->path.'/'.$image['image'],
                        'img/products/'.$product->path.'/'.$image['present_image']
                    ]);
                }
            }

            Storage::deleteDirectory('img/products/'.$product->path);
        }

        foreach ($product->products_lang as $product_lang) {
            $product_lang->delete();
        }

        $product->delete();

        return redirect($lang.'/my-ads');
    }

    public function comments($id)
    {
        $product = Product::findOrFail($id);

        return view('ads.comments', ['product' => $product]);
    }

    public function destroyComment($id)
    {
        $comment = Comment::find($id);
        $comment->delete();

        return redirect($lang.'/my-ads/'.$comment->parent_id.'/comments')->with('status', 'Запись удалена!');
    }
}
