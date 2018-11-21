<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as Roles;

class Role extends Roles
{
    
    protected $table = "tbl_roles";
    
    protected $fillable = [
        'name','guard_name'
    ];
}
