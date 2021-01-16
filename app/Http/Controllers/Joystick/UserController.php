<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use App\Http\Controllers\Joystick\Controller;

use App\User;
use App\Role;
use App\Region;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at')->paginate(50);

        return view('joystick-admin.users.index', compact('users'));
    }

    public function edit($lang, $id)
    {
        $user = User::findOrFail($id);
        $regions = Region::orderBy('sort_id')->get()->toTree();
        $roles = Role::all();

        return view('joystick-admin.users.edit', compact('user', 'regions', 'roles'));
    }

    public function update(Request $request, $lang, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:60',
            'email' => 'required',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->balance = $request->balance;
        $user->save();

        if (\Auth::user()->can(['edit-role'])) {
            $user->roles()->sync($request->roles_id);
        }

        $user->profile->phone = $request->phone;
        $user->profile->region_id = $request->region_id;
        $user->profile->birthday = $request->birthday;
        $user->profile->sex = $request->sex;
        $user->profile->about = $request->about;
        $user->profile->save();

        $user->privilege->gov_number = $request->gov_number;
        $user->privilege->card_type = $request->card_type;
        $user->privilege->barcode = $request->barcode;

        if ($request->privilege_status == 'on') {
            
            $date = date('Y-m-d');
            $term = date('Y-m-d', strtotime($date. ' + 30 days'));

            $user->privilege->term = $term;
            $user->privilege->status = 1;
        } else {
            $user->privilege->status = 0;
        }

        $user->privilege->save();

        return redirect($lang.'/admin/users')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        if (\Auth::user()->can(['delete-role'])) {
            $user = User::find($id);
            $user->profile->delete();
            $user->privilege->delete();
            $user->delete();
        } else  {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        return redirect($lang.'/admin/users')->with('status', 'Запись удалена.');
    }
}