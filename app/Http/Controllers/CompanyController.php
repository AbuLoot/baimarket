<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use App\User;
use App\Region;
use App\Company;
use App\Product;
use App\Http\Requests;

class CompanyController extends Controller
{
    public function index(Request $request, $lang)
    {
        $user = Auth::user();

        if ($user->companies->isEmpty()) {
            return redirect($lang.'/my-companies/create');
        }

        return view('company.list', compact('user'));
    }

    public function create()
    {
        $user = Auth::user();
        $regions = Region::orderBy('sort_id')->get()->toTree();

        return view('company.create', compact('user', 'regions'));
    }

    public function store(Request $request, $lang)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:companies',
        ]);

        $company = new Company;

        if ($request->hasFile('image')) {

            $logoName = str_slug($request->title).'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('img/companies', $logoName);
        }

        $company->sort_id = $company->count() + 1;
        $company->user_id = Auth::user()->id;
        $company->region_id = ($request->region_id > 0) ? $request->region_id : 0;
        $company->title = $request->title;
        $company->slug = (empty($request->slug)) ? str_slug($request->title) : $request->slug;
        $company->image = (isset($logoName)) ? $logoName : 'no-image-mini.png';
        $company->about = $request->about;
        $company->phones = $request->phones;
        $company->links = $request->links;
        $company->emails = $request->emails;
        $company->address = $request->address;
        $company->lang = $request->lang;
        $company->status = ($request->status == 'on') ? 1 : 0;
        $company->save();

        return redirect($lang.'/my-companies')->with('status', 'Запись добавлена.');
    }

    public function edit(Request $request, $lang, $id)
    {
        $user = Auth::user();
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $company = Company::find($id);

        return view('company.edit', compact('user', 'regions', 'company'));
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

            $logoName = str_slug($request->title).'.'.$request->image->getClientOriginalExtension();

            $request->image->storeAs('img/companies', $logoName);
        }

        $company->sort_id = ($request->sort_id > 0) ? $request->sort_id : $company->count() + 1;
        $company->region_id = ($request->region_id > 0) ? $request->region_id : 0;
        $company->slug = (empty($request->slug)) ? str_slug($request->title) : $request->slug;
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

        return redirect($lang.'/my-companies')->with('status', 'Запись обновлена.');
    }
}
