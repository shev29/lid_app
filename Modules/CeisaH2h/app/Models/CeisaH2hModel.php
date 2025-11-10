<?php

namespace Modules\CeisaH2h\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CeisaH2h\Database\Factories\CeisaH2hModelFactory;

class CeisaH2hModel extends Model
{
    use HasFactory;

    // use HasFactory;
    protected $table;

    // Define the attributes that are mass assignable for each table
    protected $fillable = [];

    /**
     * Set the table name and fillable attributes dynamically
     */
    public function setTableAttributes($table, $fillable)
    {
        $this->table = $table;
        $this->fillable = $fillable;
    }

    public function masterCeisaSeriEntitas()
    {
        $this->setTableAttributes('master_ceisa_seri_entitas', ['*']);
        return $this;
    }

    public function masterCeisaEntitas()
    {
        $this->setTableAttributes('master_ceisa_entitas', ['*']);
        return $this;
    }

    public function masterCeisaH2hUser()
    {
        $this->setTableAttributes('master_ceisa_h2h_user', ['*']);
        return $this;
    }

    public function masterCeisaH2hAuth()
    {
        $this->setTableAttributes('master_ceisa_h2h_auth', ['*']);
        return $this;
    }

    public function masterCeisaJenisImpor()
    {
        $this->setTableAttributes('master_ceisa_jenis_impor', ['*']);
        return $this;
    }

    public function masterCeisaCaraBayar()
    {
        $this->setTableAttributes('master_ceisa_cara_bayar', ['*']);
        return $this;
    }

    public function masterCeisaJenisIdentitas()
    {
        $this->setTableAttributes('master_ceisa_jenis_identitas', ['*']);
        return $this;
    }

    public function masterCeisaPelabuhan()
    {
        $this->setTableAttributes('master_ceisa_pelabuhan', ['*']);
        return $this;
    }

    public function masterCeisaKantor()
    {
        $this->setTableAttributes('master_ceisa_kantor', ['*']);
        return $this;
    }

    public function masterCeisaJenisEntitas()
    {
        $this->setTableAttributes('master_ceisa_jenis_entitas', ['*']);
        return $this;
    }

    public function masterCeisaJenisApiEntitas()
    {
        $this->setTableAttributes('master_ceisa_jenis_api_entitas', ['*']);
        return $this;
    }

    public function masterCeisaNegara()
    {
        $this->setTableAttributes('master_ceisa_negara', ['*']);
        return $this;
    }
    
}
