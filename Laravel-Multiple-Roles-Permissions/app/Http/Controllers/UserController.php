<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use DB;
use Spatie\Permission\Models\Role;
use Hash;

class UserController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:user-list');
        $this->middleware('permission:user-create',['only' => ['create','store']]);
        $this->middleware('permission:user-edit',['only' => ['update','edit']]);
        $this->middleware('permission:user-delete',['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::orderBy('id','DESC')->paginate(10);
        return view('users.index', compact('data'))
                ->with('i', ($request->input('page',1)-1)*10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required|min:5|max:50',
            'email'     => 'email',
            'mobile_number' => 'required|min:8|unique:tbl_users,mobile_number',
            'password'  => 'min:6|same:confirm-password',
            'roles'     => 'required'
        ]);
        
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
        
        return redirect()->route('users.index')
                ->with('Success',''.$user->name.' created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        return view('users.edit', compact('user','roles','userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'      => 'required|min:5|max:50',
            'email' => 'required|email|unique:tbl_users,email,'.$id,
            'mobile_number' => 'required|min:8|unique:tbl_users,mobile_number,'.$id,
            'roles'     => 'required',
            'password' => 'same:confirm-password',

        ]);
        
        $input = $request->all();
        if(!empty($input['password'])){
            $input = Hash::make($input['password']);
        }else{
            $input = array_except($input, array('password'));
        }
        
        $user = User::find($id);
        $user->update($input);
        
        DB::table('tbl_model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->roles);
        
        return redirect()
                ->route('users.index')
                ->with('success', ''.$user->name.' update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id)->delete();
//        $user->delete();
//        User::destroy($id);
        return redirect()
                ->route('users.index')
                ->with('success',''.$user->name.' delete successfully');
    }
}
