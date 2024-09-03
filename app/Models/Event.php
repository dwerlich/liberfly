<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'date'];

    // Coleção de Usuários vinculados ao Evento
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}

