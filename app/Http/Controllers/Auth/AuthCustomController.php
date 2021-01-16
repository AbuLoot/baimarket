<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use Auth;

use App\User;
use App\Role;
use App\Card;
use App\Profile;
use App\Privilege;
use App\Region;
use App\Http\Controllers\Controller;

class AuthCustomController extends Controller
{
    public function getLogin()
    {
        return view('account.login');
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|min:8|max:80'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/');
        }
        else {
            return redirect()->back()->withInput()->withWarning('Не правильный логин или пароль.');
        }
    }

    public function getRegister()
    {
        return view('account.register');
    }

    protected function postRegister(Request $request, $lang)
    {
        $validatedData = $this->validate($request, [
            'surname' => 'required|min:2|max:40',
            'name' => 'required|min:2|max:40',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|min:10|max:15|unique:profiles',
            'gov_number' => 'required|min:4|max:15|unique:privileges',
            'barcode' => 'required|max:255|unique:privileges',
            // 'sex' => 'required',
            // 'password' => 'required|confirmed|min:6|max:255',
            // 'rules' => 'accepted'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if ($user == true) {

            $role = Role::where('name', 'user')->first();
            $user->roles()->sync($role->id);

            $first_num = substr($request->barcode, 0, 1);
            $card_type = trans('data.card_types_number.'.$first_num);
            $card = Card::where('slug', $card_type)->first();

            $profile = new Profile;
            $profile->sort_id = $user->id;
            $profile->user_id = $user->id;
            $profile->region_id = 1;
            $profile->phone = $request->phone;
            // $profile->sex = $request['sex'];
            $profile->save();

            $privilege = new Privilege;
            $privilege->user_id = $user->id;
            $privilege->card_id = $card->id;
            $privilege->gov_number = $request->gov_number;
            $privilege->card_type = $card_type;
            $privilege->barcode = $request->barcode;
            $privilege->services = $card->service_number;
            $privilege->status = 0;
            $privilege->save();

            return redirect($lang.'/cs-login-and-register')->withInput()->withInfo('Регистрация успешно завершена. Войдите через email и пароль.');
        }
        else {
            return redirect()->back()->withInput()->withErrors('Неверные данные');
        }
    }

    public function getLoginAndRegister()
    {
        return view('login-and-register');
    }

    public function getLogout()
    {
        Auth::logout();

        return redirect('/');
    }
}
