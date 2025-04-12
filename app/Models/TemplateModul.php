<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateModul extends Model
{
    use HasFactory;
    protected $table = 'template_modul';
    protected $primaryKey = 'id_template_modul';
}
