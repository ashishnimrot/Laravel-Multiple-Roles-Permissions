<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use DB;

class RoleController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:role-list');
        $this->middleware('permission:role-create',['only' => ['create','store']]);
        $this->middleware('permission:role-edit',['only' => ['update','edit']]);
        $this->middleware('permission:role-delete',['only' => ['destroy']]);
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return view('roles.index', compact('roles'))
                ->with('i', ($request->input('page',1)-1)*5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();
        return view('roles.create', compact('permission'));
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
            'name' => 'required|unique:tbl_roles,name',
            'permission' => 'required'
        ]);
        
        $role = Role::create(['name' => $request->input('name'),'guard_name' => 'web']);
        $role->syncPermissions($request->input('permission'));
        
        return redirect()->route('roles.index')
                ->with('success','Role create successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join('tbl_role_has_permissions','permission_id','=','id')
                ->where('role_id',$id)
                ->get();
        
        return view('roles.show', compact('role','rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("tbl_role_has_permissions")->where('tbl_role_has_permissions.role_id',$id)
                ->pluck('tbl_role_has_permissions.permission_id','tbl_role_has_permissions.permission_id')
                ->all();
        
        return view('roles.edit', compact('role','permission','rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required'
        ]);
        
        $role = Role::find($id);
        $role->name = $request->name;
        $role->save();
        
        $role->syncPermissions($request->permission);
        return redirect()
                ->route('roles.index')
                ->with('success','Role update successfully');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        DB::table("roles")->where('id',$id)->delete();
        $role = Role::destroy($id);
        return redirect()
                ->route('roles.index')
                ->with('success','Role delete successfully');
    }
}
