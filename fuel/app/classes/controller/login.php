<?php

use Fuel\Core\Response;


class Controller_Login extends Controller
{
    public function action_index()
    {

        
        if(Input::method() == 'POST'){


            //ユーザーのインプットを取得
            $username = \Input::post('username');
            $password = \Input::post('password');

            //DBからユーザーを探す
            $query = DB::query("SELECT * FROM users WHERE username = :username")
            ->parameters(array('username' => $username))
            ->execute();

            if(count($query) > 0){
                $user = $query ->current();
                

                //パスワードを確認する
                if(password_verify($password,$user['password'])){
                    \Session::set('id',$user['id']);
                    \Session::set('username', $user['username']); 
                  
                    
                    \Response::redirect('weight');
                }else{
                    \Session::set_flash('error','Invalid password');
                }
            }else{
                \Session::set_flash('error', 'User not found');
            }

        }
        return Response::forge(View::forge('entry/login'));
    }

    public function action_logout()
    {
        \Session::delete('id');
        \Session::delete('username');
        \Session::destroy();
        \Cookie::delete('fuelcid'); 

        \Response::redirect('login');
    }
}
