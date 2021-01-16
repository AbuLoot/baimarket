<?php

namespace App\Http\Controllers;
use DB;
use Image;
use Storage;
use PhpQuery\PhpQuery as phpQuery;
PhpQuery::use_function(__NAMESPACE__);

use App\Option;
use App\Product;
use App\Category;
use App\ImageTrait;

use Illuminate\Http\Request;

class ParsingController extends Controller
{
    use ImageTrait;

    public $products = [];
    public $options = '';
    public $options_id = [];
    public $options_title = [];

    public function __construct()
    {
        $this->products = Product::all();
        $this->options = Option::all();
        $this->options_title = Option::pluck('title')->toArray();
    }

    public function index()
    {
    	$ch = curl_init('http://baitun.kz');

    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HEADER, true);
    	curl_setopt($ch, CURLOPT_NOBODY, true);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/60.0.3112.113 Chrome/60.0.3112.113 Safari/537.36');

    	$html = curl_exec($ch);

    	curl_close($ch);
    }

    public function request()
    {
        set_time_limit(0);

        $domen = 'https://www.malkocbebe.com';
        $url = 'https://www.malkocbebe.com/uye/giris';
        $postdata = 'kullaniciadi=globus&sifre=turktorg1&SubmitLogin=';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

        $html = curl_exec($ch);
        curl_close($ch);

        $dom = phpQuery::newDocument($html);

        $categories_except = [
            'https://www.malkocbebe.com/urunler/kategori/122',
            'https://www.malkocbebe.com/urunler/kategori/196',
            'https://www.malkocbebe.com/urunler/kategori/207',
            'https://www.malkocbebe.com/urunler/kategori/68',
            'https://www.malkocbebe.com/urunler/kategori/67',
            'https://www.malkocbebe.com/urunler/kategori/58',
            'https://www.malkocbebe.com/urunler/kategori/64',
            'https://www.malkocbebe.com/urunler/kategori/120',
            'https://www.malkocbebe.com/urunler/kategori/121',
            'https://www.malkocbebe.com/urunler/kategori/55',
            'https://www.malkocbebe.com/urunler/kategori/82',
            'https://www.malkocbebe.com/urunler/kategori/65',
            'https://www.malkocbebe.com/urunler/kategori/62',
            'https://www.malkocbebe.com/urunler/kategori/54',
            'https://www.malkocbebe.com/urunler/kategori/63',
            'https://www.malkocbebe.com/urunler/kategori/56',
            'https://www.malkocbebe.com/urunler/kategori/103',
            'https://www.malkocbebe.com/urunler/kategori/57',
            'https://www.malkocbebe.com/urunler/kategori/61',
            'https://www.malkocbebe.com/urunler/kategori/59',
            'https://www.malkocbebe.com/urunler/kategori/156',
            'https://www.malkocbebe.com/urunler/kategori/203',
            'https://www.malkocbebe.com/urunler/kategori/66',
            'https://www.malkocbebe.com/urunler/kategori/177',
            'https://www.malkocbebe.com/urunler/kategori/178',
            'https://www.malkocbebe.com/urunler/kategori/200',
            'https://www.malkocbebe.com/urunler/kategori/146',
            'https://www.malkocbebe.com/urunler/kategori/117',
            'https://www.malkocbebe.com/urunler/kategori/145',
            'https://www.malkocbebe.com/urunler/kategori/70',
            'https://www.malkocbebe.com/urunler/kategori/71',
            'https://www.malkocbebe.com/urunler/kategori/123',
            'https://www.malkocbebe.com/urunler/kategori/147',
            'https://www.malkocbebe.com/urunler/kategori/124',
            'https://www.malkocbebe.com/urunler/kategori/118',
            'https://www.malkocbebe.com/urunler/kategori/73',
            'https://www.malkocbebe.com/urunler/kategori/75',
            'https://www.malkocbebe.com/urunler/kategori/195',
            'https://www.malkocbebe.com/urunler/kategori/77',
            'https://www.malkocbebe.com/urunler/kategori/80',
            'https://www.malkocbebe.com/urunler/kategori/193',
            'https://www.malkocbebe.com/urunler/kategori/81',
            'https://www.malkocbebe.com/urunler/kategori/76',
            'https://www.malkocbebe.com/urunler/kategori/119',
            'https://www.malkocbebe.com/urunler/kategori/72',

            'https://www.malkocbebe.com/urunler/kategori/143',
            'https://www.malkocbebe.com/urunler/kategori/94',
            'https://www.malkocbebe.com/urunler/kategori/142',

            'https://www.malkocbebe.com/urunler/kategori/95',
            'https://www.malkocbebe.com/urunler/kategori/92',
            'https://www.malkocbebe.com/urunler/kategori/140',
            'https://www.malkocbebe.com/urunler/kategori/97',
            'https://www.malkocbebe.com/urunler/kategori/98',
            'https://www.malkocbebe.com/urunler/kategori/99',
            'https://www.malkocbebe.com/urunler/kategori/139',
            'https://www.malkocbebe.com/urunler/kategori/213',
            'https://www.malkocbebe.com/urunler/kategori/214',
            'https://www.malkocbebe.com/urunler/kategori/141',
            'https://www.malkocbebe.com/urunler/kategori/128',
        ];

        foreach ($dom->find('.wsmenu>.wsmenu-list>li>.wsmegamenu .link-list li a') as $category) {

            $category_item = pq($category);
            $category_href = $category_item->attr('href');

            // echo $category_href.'<br>';
            if (!in_array($category_href, $categories_except)) {
                $this->recursive_get_category($category_href);
            }
        }

        echo '<h1>The end!</h1>';
    }

    public function recursive_get_category($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/cookies.txt');
        $html = curl_exec($ch);
        curl_close($ch);

        $dom = phpQuery::newDocument($html);

        foreach ($dom->find('.pnlurun .urunkutu-detay .isim a') as $product) {
            $product_item = pq($product);
            $product_href = $product_item->attr('href');
            $response = $this->recursive_get_product($product_href);
            if ($response == false) {
                continue;
            }
            usleep(300000);
        }

        $active_page = $dom->find('.pagination>.active')->next();
        $next_page = $active_page->find('a')->attr('href');

        if ( ! empty($next_page)) {
            $this->recursive_get_category($next_page);
        }

        phpQuery::unloadDocuments($html);
    }

    public function recursive_get_product($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/cookies.txt');
        $html = curl_exec($ch);
        curl_close($ch);

        $dom = phpQuery::newDocument($html);

        $product_item = $dom->find('#serwrapper')->html();
        $page = pq($product_item);

        $title = $page->find('h1')->text();

        if (Product::where('title', $title)->first()) return false;

        $barcode = $page->find('table tbody tr:eq(0) td:eq(1)')->text();
        $category_title = $page->find('table tbody tr:eq(1) td:eq(1) a')->text();
        $category_parent_title = $page->find('a.op5:eq(1))')->text();
        $age_group = $page->find('table tbody tr:eq(2) td:eq(1)')->text();
        $package_qty = $page->find('table tbody tr:eq(3) td:eq(1)')->text();
        $description = $page->find('table tbody tr:eq(4) td:eq(1)')->text();
        $price = $page->find('.detay-pano div div span')->text();
        $price2 = $page->find('.detay-pano div div div')->text();

        echo $title.
            '<br> barcode: '.$barcode.
            '| category: '.$category_title.
            '| category parent: '.$category_parent_title.
            '| age_group: '.$age_group.
            '| package_qty: '.$package_qty.
            '| description: '.$description.
            '| price: '.$price.
            '| price2: '.$price2.'<hr>';

        $category_parent = Category::where('title', $category_parent_title)->first();

        if ($category_parent == NULL) {
            $category_parent = $this->createCategory($category_parent_title, NULL);
        }

        $category = Category::where('title', $category_title)->where('parent_id', $category_parent->id)->first();

        if ($category == NULL) {
            $category = $this->createCategory($category_title, $category_parent);
        }

        $introImage = null;
        $images = [];
        $order = 0;
        $dirName = $category->id.'/'.time();
        Storage::makeDirectory('img/products/'.$dirName);

        foreach ($page->find('div.items img.img-responsive') as $key => $elem)
        {
            $image_data = pq($elem);
            $image_src = $image_data->attr('data-cfsrc');

            if (empty($image_src)) {
                continue;
            }

            // $headers = get_headers($image_src);
            // $response = substr($headers[0], 9, 3);
            $image_org = file_get_contents($image_src);

            // if ($headers[0] == "HTTP / 1.1 200 OK") {
            //     $image_org = file_get_contents($image_src);
            // }
            // else {
            // // elseif (!$image_org) {
            //     $image_org = Storage::get('img/no-image-big.png');
            // }

            $image_ext = pathinfo($image_src, PATHINFO_EXTENSION);
            $imageName = 'image-'.$order.'-'.str_slug($title).'.'.$image_ext;

            $watermark = Image::make('img/watermark.png');

            // Creating main images
            $this->resizeOptimalImage($image_org, 1200, 900, '/img/products/'.$dirName.'/'.$imageName, 95, $watermark);

            $watermark = Image::make('img/Untitled.png');

            // Creating present images
            $this->resizeOptimalImage($image_org, 300, 280, '/img/products/'.$dirName.'/present-'.$imageName, 95, $watermark);

            // Creating preview image
            if ($order == 0) {
                $introImage = 'present-'.$imageName;
            }

            $images[$order]['image'] = $imageName;
            $images[$order]['present_image'] = 'present-'.$imageName;
            $order++;
        }

        $product = new Product;
        $product->sort_id = $product->count() + 1;
        $product->category_id = $category->id;
        $product->slug = str_slug($title);
        $product->title = $title;
        $product->meta_title = $title;
        $product->meta_description = $title.' '.$category_title;
        $product->company_id = 0;
        $product->barcode = $barcode;
        $product->price = rtrim($price, " ₺");;
        $product->days = 1;
        $product->count = $package_qty;
        // $product->condition = $request->condition;
        // $product->availability = $request->availability;
        $product->oem = $price2;
        $product->description = $description;
        $product->characteristic = $age_group;
        $product->image = $introImage;
        $product->images = serialize($images);
        $product->path = $dirName;
        $product->lang = 'tr';
        $product->status = 1;
        $product->save();

        $this->options = Option::all();
        $this->options_title = Option::pluck('title')->toArray();

        // Select colors
        foreach ($page->find('select optgroup option') as $key => $elem)
        {
            $elem = pq($elem);

            if (in_array($elem->text(), $this->options_title)) {
                $option_id = $this->options->where('title', $elem->text())->pluck('id')->toArray();
                if (isset($option_id[0])) {
                    $this->options_id[] = $option_id[0];
                }
            }
            else {
                $this->options_id[] = $this->createOption($elem->text());
            }

            array_unique($this->options_id);
        }

        if ( ! empty($this->options_id)) {
            $product->options()->attach($this->options_id);
        }

        unset($this->options_id);

        phpQuery::unloadDocuments($html);
    }

    public function createCategory($title, $category_parent)
    {
        $category = new Category;
        $category->sort_id = $category->count() + 1;
        $category->slug = str_slug($title);
        $category->title = $title;
        $category->title_extra = '';
        $category->image = 'no-image-mini.png';
        $category->meta_title = $title;
        $category->meta_description = $title;

        if ($category_parent == NULL) {
            $category->saveAsRoot();
        }
        else {
            $category->appendToNode($category_parent)->save();
        }

        $category->lang = 'tr';
        $category->status = 1;
        $category->save();

        return $category;
    }

    public function createOption($title)
    {
        $option = new Option;
        $option->sort_id = $option->count() + 1;
        $option->slug = str_slug($title);
        $option->title = $title;
        $option->data = 'Color';
        $option->lang = 'tr';
        $option->save();

        return $option->id;
    }
}
?>