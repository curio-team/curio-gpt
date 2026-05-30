<?php

namespace App\Models;

use Database\Factories\AgentConfigFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'description', 'instructions', 'created_by', 'allowed_groups', 'allowed_models', 'image_path', 'is_enabled', 'history_is_disabled', 'turn_limit', 'available_from', 'available_until', 'monitoring_is_enabled', 'monitoring_instructions', 'monitoring_model', 'attachments'])]
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
     * TODO: For future implementation, we might want to add a column image_url to the agent_configs table and remove this.
     * Right now only here to silence PHPStan.
     *
     * @var string|null
     */
    public $image_url = null;

    /**
     * The attributes that should be cast to native types.
     *
     * @return array{
     *     allowed_groups: 'array',
     *     allowed_models: 'array',
     *     attachments: 'array',
     *     is_enabled: 'boolean',
     *     history_is_disabled: 'boolean',
     *     turn_limit: 'integer',
     *     monitoring_is_enabled: 'boolean'
     * }
     */
    protected function casts(): array
    {
        return [
            'allowed_groups' => 'array',
            'allowed_models' => 'array',
            'attachments' => 'array',
            'is_enabled' => 'boolean',
            'history_is_disabled' => 'boolean',
            'turn_limit' => 'integer',
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
        return Attribute::get(function () {
            if ($this->image_path) {
                /** @var FilesystemAdapter $disk */
                $disk = Storage::disk('public');

                return $disk->url($this->image_path);
            }

            return null;
        });
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
