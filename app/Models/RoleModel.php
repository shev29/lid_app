<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table;

    // protected $fillable = [];
    protected $guarded = [];

    public function setTableAttributes($table, $guard) {
        $this->table = $table;
        $this->guard = $guard;
    }

    public function masterMenus() {
        $this->setTableAttributes('master_menus', []);
        return $this;
    }

    public function masterSubmenus() {
        $this->setTableAttributes('master_submenus', []);
        return $this;
    }

    public function masterRole() {
        $this->setTableAttributes('master_role', []);
        return $this;
    }

    public function masterRoleDetails() {
        $this->setTableAttributes('master_role_details', []);
        return $this;
    }

    public function masterRoleUsers() {
        $this->setTableAttributes('master_role_users', []);
        return $this;
    }
}
