// app/Models/VM.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VM extends Model
{
    protected $fillable = [
        'vmid',
        'name',
        'tatus',
    ];
}
