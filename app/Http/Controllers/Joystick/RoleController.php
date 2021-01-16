<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use App\Http\Controllers\Joystick\Controller;
use App\Role;
use App\Permission;

class RoleController extends Controller
{
    public function index()
    {
    	$roles = Role::all();

        return view('joystick-admin.roles.index', compact('roles'));
    }

    public function create($lang)
    {
        if (!\Auth::user()->can(['create-role'])) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $permissions = Permission::all();

        return view('joystick-admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can(['create-role'])) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $this->validate($request, [
            'name' => 'required|max:60|unique:roles',
        ]);

        $role = new Role;
        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->save();

        $role->perms()->sync($request->permissions_id);

        return redirect($request->lang.'/admin/roles')->with('status', 'Запись добавлена!');
    }

    public function edit($lang, $id)
    {
        if (!\Auth::user()->can(['edit-role'])) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $role = Role::findOrFail($id);
        $permissions = Permission::all();

        return view('joystick-admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $lang, $id)
    {
        if (!\Auth::user()->can(['edit-role'])) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $this->validate($request, [
            'name' => 'required|max:60',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->perms()->sync($request->permissions_id);
        $role->save();

        return redirect($lang.'/admin/roles')->with('status', 'Запись обновлена!');
    }

    public function destroy($lang, $id)
    {
        if (!\Auth::user()->can(['delete-role'])) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

        $role = Role::findOrFail($id);
        $role->delete();

        return redirect($lang.'/admin/roles')->with('status', 'Запись удалена!');
    }
}