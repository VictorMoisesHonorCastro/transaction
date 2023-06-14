<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(){


    }
    public function create(Request $request){
        try{
            DB::beginTransaction();
            $request->validate([
                'name' =>'required',
                'email' =>'required|email',
                'telefono'=>'required',
                'password' =>'required',
            ]);
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->telefono = $request->telefono;
            $user->password = Hash::make($request->password);
            $user->save();
            DB::commit();
            return response()->json([
                "status"=>"success",
                "message"=>"Successfully created user!",
                "value"=>$user
            ]);
        }catch (\Exception $exp){
            DB::rollBack();
            return response()->json([
                "status"=>"error",
                "message"=>$exp->getMessage()
            ]);
        }
    }
    public function login(Request $request){
        try{
            $request->validate([
                "email"=>"required | email",
                "password"=>"required"
            ]);
            DB::beginTransaction();
            $user = User::where("email", $request->email)->first();
            /*if(isset($user)){*/
                if(Hash::check($request->password, $user->password)){
                    $tokken = $user->createToken("auth_token")->plainTextToken;
                    DB::commit();
                    return response()->json([
                        "status"=>"success",
                        "message"=>"Successfully logged in",
                        "token"=>$tokken
                    ]);
                }else{
                    DB::rollBack();
                    return response()->json([
                        "status"=>"error",
                        "message"=>"Invalid Password"
                    ]);
                }
            /*}else{
                DB::rollBack();
                return response()->json([
                    "status"=>"error",
                    "message"=>"Invalid email!"
                ]);
            }*/
        }catch (\Exception $exp){
            return response()->json([
                "status"=>"error",
                "message"=>$exp->getMessage()
            ]);
        }
    }
    public function update( Request $request, $id){

    }
    public function logout(){

    }

}
