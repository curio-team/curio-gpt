<?php

namespace App\Models;

use Database\Factories\AgentConfigFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['name', 'instructions', 'created_by', 'allowed_groups'])]
class AgentConfig extends Model
{
    /** @use HasFactory<AgentConfigFactory> */
    use HasFactory;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'allowed_groups' => 'array',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (AgentConfig $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid7();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
