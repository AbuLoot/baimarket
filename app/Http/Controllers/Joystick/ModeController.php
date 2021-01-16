<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Mode;
use App\Language;

class ModeController extends Controller
{
    public function index()
    {
        $modes = Mode::orderBy('sort_id')->paginate(50);

        return view('joystick-admin.modes.index', compact('modes'));
    }

    public function create($lang)
    {
        $languages = Language::get();

        return view('joystick-admin.modes.create', ['languages' => $languages]);
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create-role')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:modes',
        ]);

        $mode = new Mode;
        $titles[$request->lang]['title'] = $request->title;
        $languages[$request->lang] = $request->lang;

        $mode->sort_id = ($request->sort_id > 0) ? $request->sort_id : $mode->count() + 1;
        $mode->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $mode->title = serialize($titles);
        $mode->data = $request->data;
        $mode->lang = serialize($languages);
        $mode->status = ($request->status == 'on') ? 1 : 0;
        $mode->save();

        return redirect($request->lang.'/admin/modes')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $mode = Mode::findOrFail($id);
        $languages = unserialize($mode->lang);

        if ( ! in_array($lang, $languages)) {
            return view('joystick-admin.modes.create-lang', compact('mode'));
        }

        return view('joystick-admin.modes.edit', compact('mode'));
    }

    public function update(Request $request, $lang, $id)
    {
        if (!\Auth::user()->can('edit-role')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $mode = Mode::findOrFail($id);

        $titles = unserialize($mode->title);
        $languages = unserialize($mode->lang);

        $titles[$lang]['title'] = $request->title;
        $languages[$lang] = $lang;

        if (empty($request->title)) {
            unset($titles[$lang]['title']);
            unset($languages[$lang]);
        }

        $mode->sort_id = ($request->sort_id > 0) ? $request->sort_id : $mode->count() + 1;
        $mode->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $mode->title = serialize($titles);
        $mode->data = $request->data;
        $mode->lang = serialize($languages);
        $mode->status = ($request->status == 'on') ? 1 : 0;
        $mode->save();

        return redirect($lang.'/admin/modes')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        if (!\Auth::user()->can('delete-role')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $mode = Mode::find($id);
        $mode->delete();

        return redirect($lang.'/admin/modes')->with('status', 'Запись удалена!');
    }
}
