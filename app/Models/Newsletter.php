<?php

namespace App\Models;

use App\Actions\GenerateNewsletterAudienceAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\ModelStatus\HasStatuses;

class Newsletter extends Model
{
    use HasFactory;
    use HasStatuses;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emails(): BelongsToMany
    {
        return $this
            ->belongsToMany(Email::class)
            ->withPivot([
                'status',
                'found_at_mailbox',
            ]);
    }

    public static function audienceFields(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
        ];
    }

    public function getOrGenerateAudience(): array
    {
        return $this->emails->count()
            ? $this->emails->toArray()
            : app(GenerateNewsletterAudienceAction::class)($this);
    }
}
