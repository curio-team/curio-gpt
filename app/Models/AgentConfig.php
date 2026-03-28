<?php

namespace App\Models;

use Database\Factories\AgentConfigFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'description', 'instructions', 'created_by', 'allowed_groups', 'allowed_models', 'image_path', 'is_enabled', 'history_is_disabled', 'available_from', 'available_until', 'monitoring_is_enabled', 'monitoring_instructions', 'monitoring_model', 'attachments'])]
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
            'allowed_models' => 'array',
            'attachments' => 'array',
            'is_enabled' => 'boolean',
            'history_is_disabled' => 'boolean',
            'monitoring_is_enabled' => 'boolean',
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

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null);
    }

    public function isCurrentlyAvailable(): bool
    {
        if (! $this->is_enabled) {
            return false;
        }

        if ($this->available_from === null && $this->available_until === null) {
            return true;
        }

        $now = now()->format('H:i:s');

        if ($this->available_from !== null && $this->available_until !== null) {
            return $now >= $this->available_from && $now <= $this->available_until;
        }

        if ($this->available_from !== null) {
            return $now >= $this->available_from;
        }

        return $now <= $this->available_until;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
