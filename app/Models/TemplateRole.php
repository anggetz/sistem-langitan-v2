<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateRole extends Model
{
    use HasFactory;
    protected $table = 'template_role';
    protected $primaryKey = 'id_template_role';
}
