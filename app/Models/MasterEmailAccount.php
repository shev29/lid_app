<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterEmailAccount extends Model
{
    use HasFactory;

    protected $table = 'master_email_accounts';

    protected $fillable = [
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'username',
        'password',
        'display_name',
        'reply_to',
    ];
}