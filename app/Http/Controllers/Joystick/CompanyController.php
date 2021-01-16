<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Joystick\Controller;
use App\Region;
use App\Company;

use Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::orderBy('sort_id')->paginate(50);

        return view('joystick-admin.companies.index', compact('companies'));
    }

    public function actionCompanies(Request $request)
    {
        $this->validate($request, [
            'companies_id' => 'required'
        ]);

        Company::whereIn('id', $request->companies_id)->update(['status' => $request->action]);

        return response()->json(['status' => true]);
    }

    public function create($lang)
    {
        $regions = Region::orderBy('sort_id')->get()->toTree();

        return view('joystick-admin.companies.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:companies',
        ]);

        $company = new Company;

        if ($request->hasFile('image')) {

            $logoName = Str::slug($request->title).'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('img/companies', $logoName);
        }

        $company->sort_id = ($request->sort_id > 0) ? $request->sort_id : $company->count() + 1;
        $company->region_id = ($request->region_id > 0) ? $request->region_id : 0;
        $company->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $company->title = $request->title;
        $company->image = (isset($logoName)) ? $logoName : 'no-image-mini.png';
        $company->about = $request->about;
        $company->phones = $request->phones;
        $company->links = $request->links;
        $company->emails = $request->emails;
        $company->address = $request->address;
        $company->lang = $request->lang;
        $company->status = ($request->status == 'on') ? 1 : 0;
        $company->save();

        return redirect($request->lang.'/admin/companies')->with('status', 'Запись добавлена.');
    }

    public function edit($lang, $id)
    {
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $company = Company::findOrFail($id);

        return view('joystick-admin.companies.edit', compact('regions', 'company'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $company = Company::findOrFail($id);

        if ($request->hasFile('image')) {

            if (file_exists($company->image)) {
                Storage::delete($company->image);
            }

            $logoName = Str::slug($request->title).'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('img/companies', $logoName);
        }

        $company->sort_id = ($request->sort_id > 0) ? $request->sort_id : $company->count() + 1;
        $company->region_id = ($request->region_id > 0) ? $request->region_id : 0;
        $company->slug = (empty($request->slug)) ? Str::slug($request->title) : $request->slug;
        $company->title = $request->title;
        if (isset($logoName)) $company->image = $logoName;
        $company->about = $request->about;
        $company->phones = $request->phones;
        $company->links = $request->links;
        $company->emails = $request->emails;
        $company->address = $request->address;
        $company->lang = $request->lang;
        $company->status = ($request->status == 'on') ? 1 : 0;
        $company->save();

        return redirect($lang.'/admin/companies')->with('status', 'Запись обновлена.');
    }

    public function destroy($lang, $id)
    {
        $company = Company::find($id);

        if (file_exists('img/companies/'.$company->image)) {
            Storage::delete('img/companies/'.$company->image);
        }

        $company->delete();

        return redirect($lang.'/admin/companies')->with('status', 'Запись удалена.');
    }
}