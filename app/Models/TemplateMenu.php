<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateMenu extends Model
{
    use HasFactory;
    protected $table = 'template_menu';
    protected $primaryKey = 'id_template_menu';
}
