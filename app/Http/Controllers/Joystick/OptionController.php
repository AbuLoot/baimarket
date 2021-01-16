<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Option;
use App\Language;

class OptionController extends Controller
{
    public function index()
    {
        $options = Option::orderBy('sort_id')->paginate(50);
        $languages = Language::get();

        return view('joystick-admin.options.index', compact('options', 'languages'));
    }

    public function create($lang)
    {
        $languages = Language::get();

        return view('joystick-admin.options.create', ['languages' => $languages]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:1|max:100',
            'data' => 'required|min:1|max:100',
        ]);

        $titles = [];
        $data = [];
        $languages = [];

        $option = new Option;
        $titles[$request->lang]['title'] = $request->title;
        $data[$request->lang]['data'] = $request->data;
        $languages[$request->lang] = $request->lang;

        $option->sort_id = ($request->sort_id > 0) ? $request->sort_id : $option->count() + 1;
        $option->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $option->title = serialize($titles);
        $option->data = serialize($data);
        $option->lang = serialize($languages);
        $option->save();

        return redirect($request->lang.'/admin/options')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $option = Option::findOrFail($id);
        $languages = unserialize($option->lang);

        if ( ! in_array($lang, $languages)) {
            return view('joystick-admin.options.create-lang', compact('option'));
        }

        return view('joystick-admin.options.edit', compact('option'));
    }

    public function update(Request $request, $lang, $id)
    {
        $option = Option::findOrFail($id);

        $titles = unserialize($option->title);
        $data = unserialize($option->data);
        $languages = unserialize($option->lang);

        $titles[$lang]['title'] = $request->title;
        $data[$lang]['data'] = $request->data;
        $languages[$lang] = $lang;

        if (empty($request->title)) {
            unset($titles[$lang]['title']);
            unset($data[$lang]['data']);
            unset($languages[$lang]);
        }

        $option->sort_id = ($request->sort_id > 0) ? $request->sort_id : $option->count() + 1;
        $option->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $option->title = serialize($titles);
        $option->data = serialize($data);
        $option->lang = serialize($languages);
        $option->save();

        return redirect($lang.'/admin/options')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        $option = Option::find($id);
        $option->delete();

        return redirect($lang.'/admin/options')->with('status', 'Запись удалена!');
    }
}
