<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Section;

class SectionController extends Controller
{
    public function index()
    {
        $section = Section::orderBy('sort_id')->get();

        return view('joystick-admin.section.index', compact('section'));
    }

    public function create($lang)
    {
        return view('joystick-admin.section.create');
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create-role')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $this->validate($request, [
            'title' => 'required|min:5|max:80|unique:section',
        ]);

        $data = [];

        for ($i = 0; $i < count($request->data['key']); $i++) {
            $data[$i]['key'] = $request->data['key'][$i];
            $data[$i]['value'] = $request->data['value'][$i];
        }

        $part = new Section;

        $part->sort_id = ($request->sort_id > 0) ? $request->sort_id : $part->count() + 1;
        $part->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $part->title = $request->title;
        $part->meta_title = NULL;
        $part->meta_description = NULL;
        $part->image = NULL;
        $part->images = NULL;
        $part->data_1 = serialize($data[0]);
        $part->data_2 = serialize($data[1]);
        $part->data_3 = serialize($data[2]);
        $part->content = $request->content;
        $part->lang = $request->lang;
        $part->status = ($request->status == 'on') ? 1 : 0;
        $part->save();

        return redirect($request->lang.'/admin/section')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        $part = Section::findOrFail($id);

        return view('joystick-admin.section.edit', compact('part'));
    }

    public function update(Request $request, $lang, $id)
    {
        if (!\Auth::user()->can('edit-role')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $data = [];

        for ($i = 0; $i < count($request->data['key']); $i++) {
            $data[$i]['key'] = $request->data['key'][$i];
            $data[$i]['value'] = $request->data['value'][$i];
        }

        $part = Section::findOrFail($id);
        $part->sort_id = ($request->sort_id > 0) ? $request->sort_id : $part->count() + 1;
        $part->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $part->title = $request->title;
        $part->image = NULL;
        $part->images = NULL;
        $part->data_1 = serialize($data[0]);
        $part->data_2 = serialize($data[1]);
        $part->data_3 = serialize($data[2]);
        $part->content = $request->content;
        $part->lang = $request->lang;
        $part->status = ($request->status == 'on') ? 1 : 0;
        $part->save();

        return redirect($lang.'/admin/section')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        if (!\Auth::user()->can('delete-role')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $part = Section::find($id);
        $part->delete();

        return redirect($lang.'/admin/section')->with('status', 'Запись удалена!');
    }
}
