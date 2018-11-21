<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission as Permissions;

class PermissionController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:permission-list');
        $this->middleware('permission:permission-create',['only' => ['create','store']]);
        $this->middleware('permission:permission-edit',['only' => ['update','edit']]);
        $this->middleware('permission:permission-delete',['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permissions::paginate(5);
        return view('permissions.index', compact('permissions'))
                ->with('i',(request()->input('page',1)-1)*5);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permissions.create');
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
            'name' => 'required|unique:tbl_permissions,name',
            'guard_name' => 'required'
        ]);
        
        $permission = Permissions::create($request->all());
        return redirect()
                ->route('permissions.index')
                ->with('success','Permission '.$request->name.' created successfully !');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        //
    }
}
