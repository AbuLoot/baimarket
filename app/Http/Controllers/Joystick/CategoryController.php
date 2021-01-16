<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Category;

use Storage;

class CategoryController extends Controller
{
    public function index()
    {
        // $categories = Category::get()->toTree();
        $categories = Category::with('ancestors')->paginate(50)->toTree();

        return view('joystick-admin.categories.index', compact('categories'));
    }

    public function actionCategories(Request $request)
    {
        $this->validate($request, [
            'categories_id' => 'required'
        ]);

        Category::whereIn('id', $request->categories_id)->update(['status' => $request->action]);

        return response()->json(['status' => true]);
    }

    public function create($lang)
    {
        $categories = Category::get()->toTree();

        return view('joystick-admin.categories.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create-role')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $category = new Category;
        $category->sort_id = ($request->sort_id > 0) ? $request->sort_id : $category->count() + 1;
        $category->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $category->title = $request->title;
        $category->title_extra = $request->title_extra;
        $category->image = (isset($request->image)) ? $request->image : 'no-image-mini.png';
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        $parent_node = Category::find($request->category_id);

        if (is_null($parent_node)) {
            $category->saveAsRoot();
        }
        else {
            $category->appendToNode($parent_node)->save();
        }

        $category->lang = $request->lang;
        $category->status = $request->status;
        $category->save();

        return redirect($request->lang.'/admin/categories')->with('status', 'Запись добавлена.');
    }

    public function edit($lang, $id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::get()->toTree();

        return view('joystick-admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, $lang, $id)
    {
        if (!\Auth::user()->can('edit-role')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $category = Category::findOrFail($id);
        $category->sort_id = ($request->sort_id > 0) ? $request->sort_id : $category->count() + 1;
        $category->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $category->title = $request->title;
        $category->title_extra = $request->title_extra;
        $category->image = (isset($request->image)) ? $request->image : 'no-image-mini.png';
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        $parent_node = Category::find($request->category_id);

        if (is_null($parent_node)) {
            $category->saveAsRoot();
        }
        elseif ($category->id != $request->category_id) {
            $category->appendToNode($parent_node)->save();
        }

        $category->lang = $request->lang;
        $category->status = $request->status;
        $category->save();

        return redirect($lang.'/admin/categories')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        if (!\Auth::user()->can('delete-role')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $category = Category::find($id);
        $category->delete();

        return redirect($lang.'/admin/categories')->with('status', 'Запись удалена.');
    }
}
