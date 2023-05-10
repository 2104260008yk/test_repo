<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Pictures;
use Illuminate\Support\Facades\Storage;


class ProductionController extends Controller
{
    // laravel
    // git test
    
    public function index(Request $request){
        return view('production.top.Pics');
    }

    public function login(Request $request){
        return view('production.top.login');
    }

    public function login_send(Request $request){
        $param = [
            'user_id' => $request->user_id,
            'password' => $request->password,
        ];
        // $user_name = DB::select('select * from members where user_id = :user_id & password = :password',$param);
        $user_std = DB::table('members')
                    ->where('user_id','=',$request->user_id)
                    ->where('password', '=', $request->password)
                    ->get();
        $user_array = json_decode(json_encode($user_std), true);
        $test = (array)$user_array;
        if(isset($test[0]["user_name"])){
            $request->session()->put('user_name',$test[0]["user_name"]);
            $request->session()->put('user_id',$request->user_id);
            $request->session()->put('password',$request->password);
            return redirect('/mypage');    
        }else{
            return view('production.top.login',['msg'=>'IDまたはパスワードが一致していません。']);
        }
    }

    public function new_account(Request $request){
        return view('production.top.new_account');
    }

    public function register(Request $request){
        $param = [
            'user_name' => $request->user_name,
            'user_id' => $request->user_id,
            'password' => $request->password,
        ];
        DB::insert('insert into members(user_name,user_id,password) values(:user_name,:user_id,:password)',$param);
        $request->session()->put('user_name',$request->user_name);
        $request->session()->put('user_id',$request->user_id);
        $request->session()->put('password',$request->password);
        return redirect('/mypage');
    }

    public function mypage(Request $request){
        $request->session()->all();
        $user_id = session('user_id');
        $pictures = Storage::disk('public')->files("pics/{$user_id}");
        session()->put('pictures',$pictures);
        $all_pics = Storage::allFiles("public/pics");
        return view('production.in.mypage',['pictures'=>$pictures,'all_pics'=>$all_pics]);
    }

    public function post(Request $request){
        $request->session()->all();
        return view('production.in.post');
    }

    public function posted(Request $request){
        // 参考サイト　https://migisanblog.com/laravel-image-upload-view/
        // https://taidanahibi.com/laravel/upload-image/#index_id5
        $request->session()->all();

        $dir = 'pics/all/';
        $file_name = $request->file('pic_id')->getClientOriginalName();
        $request->file('pic_id')->storeAs('public/'. $dir , $file_name);

        $dir = 'pics/'.session('user_id');
        $file_name = $request->file('pic_id')->getClientOriginalName();
        $request->file('pic_id')->storeAs('public/'. $dir , $file_name);

        // DBに保存
        $pic = new Pictures();
        $pic->user_id = session('user_id');
        $pic->pic_id = $request->pic_id;
        $pic->pic_name = $request->pic_name;
        $pic->path = 'storage/'.$dir.'/'.$file_name;
        $pic->pic_content = $request->pic_content;
        $pic->save = $request->save;
        $pic->save();

        return redirect('/mypage');
    }

    public function check(Request $request){
        $request->session()->all();
        return view('production.in.check');
    }

    public function logout(Request $request){
        $request->session()->flush();
        return redirect('/pics');
        #return redirect('login');
    }

    public function change_name(Request $request){
        $request->session()->all();
        return view('production.in.change.name');
    }

    public function changed_name(Request $request){
        $request->session()->all();
        DB::table('members')
            ->where('user_id','=',session('user_id'))
            ->where('password','=',session('user_password'))
            ->update([
                'user_name' => $request->new_user_name,
            ]);
            $request->session()->put('user_name',$request->new_user_name);
            $msg = "ユーザー名の変更が完了しました！";
            #$msg = $user_std;
            return view('production.in.mypage',['msg'=>$msg,'pictures'=>session('pictures')]);
    }


    
    public function change_passwd(Request $request){
        $request->session()->all();
        return view('production.in.change.passwd');
    }

    public function changed_passwd(Request $request){
        $request->session()->all();
        $user_std = DB::table('members')
                    ->where('user_id','=',session('user_id'))
                    ->where('password', '=', $request->password)
                    ->get();
        if(empty($user_std[0])){
            $msg = "元のパスワードを間違えています。もう一度入力してください。";
            #$msg = $user_std[0];
            return view('production.in.change.passwd',['msg'=>$msg]);
        }elseif($request->new_passwd1 == $request->new_passwd2){
            $user_std = DB::table('members')
                        ->where('user_id','=',session('user_id'))
                        ->where('password','=',$request->password)
                        ->update([
                            'password' => $request->new_passwd1
                        ]);
            $request->session()->put('password',$request->new_passwd1);
            $msg = "パスワードの変更が完了しました！";
            #$msg = $user_std;
            return view('production.in.mypage',['msg'=>$msg,'pictures'=>session('pictures')]);
        }else{
            $msg = "１回目の入力と２回目の入力のパスワードが一致しません。もう一度入力してください。";
            return view('production.in.change.passwd',['msg'=>$msg]);
        }
        return view('production.in.change.passwd');
    }

    public function delete(Request $request){
        $request->session()->all();
        return view('production.in.del');
    }

    public function deleted(Request $request){
        $request->session()->all();
        if($request->send == 'no'){
            $msg = "退会処理を中止しました。";
            return view('production.in.mypage',['msg'=>$msg]);
        }else{
            DB::table('members')
                ->where('user_id','=',session('user_id'))
                ->delete();
            DB::table('pictures')
                ->where('user_id','=',session('user_id'))
                ->delete();
            $file = 'public/pics/'.session('user_id');
            Storage::deleteDirectory($file);
            $request->session()->flush();
            return view('production.top.pics',['msg'=>'退会処理を行いました']);
        }
        return view('production.in.del',['msg'=>$request->send]);
    }
}
