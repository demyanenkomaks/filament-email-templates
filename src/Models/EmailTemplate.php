<?php

namespace Maksde\FilamentEmailTemplates\Models;

use Exception;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder as EBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maksde\FilamentEmailTemplates\Database\Factories\EmailTemplateFactory;
use RuntimeException;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $key
 * @property string $language
 * @property string $name
 * @property array $from
 * @property array $cc
 * @property array $bcc
 * @property string $subject
 * @property string $content
 */
class EmailTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'language',
        'name',
        'from',
        'cc',
        'bcc',
        'subject',
        'content',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTableFromConfig();
    }

    protected static function boot(): void
    {
        parent::boot();

        // Когда шаблон электронного письма обновляется
        static::updated(function ($template): void {
            self::clearEmailTemplateCache($template->key, $template->language);
        });

        // При удалении шаблона электронного письма
        static::deleted(function ($template): void {
            self::clearEmailTemplateCache($template->key, $template->language);
        });
    }

    public function setTableFromConfig(): void
    {
        $this->table = config('filament-email-templates.table_name');
    }

    public static function findEmailByKey($key, $language = null)
    {
        $cacheKey = sprintf('email_by_key_%s_%s', $key, $language);

        return Cache::remember($cacheKey, now()->addMinutes(60), static function () use ($key, $language) {
            return self::query() // @phpstan-ignore method.notFound
                ->language($language ?? config('filament-email-templates.default_locale'))
                ->where('key', $key)
                ->firstOrFail();
        });
    }

    public static function clearEmailTemplateCache($key, $language): void
    {
        $cacheKey = sprintf('email_by_key_%s_%s', $key, $language);
        Cache::forget($cacheKey);
    }

    protected static function newFactory(): EmailTemplateFactory
    {
        return EmailTemplateFactory::new();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name ?? class_basename($this);
    }

    /**
     * Эффективный метод возврата запрошенного шаблона локали или шаблона языка по умолчанию в одном запросе
     */
    #[Scope]
    protected function language(EBuilder $query, $language): EBuilder
    {
        $languages = [$language, config('filament-email-templates.default_locale')];

        return $query->whereIn('language', $languages) // @phpstan-ignore return.type
            ->orderByRaw(
                '(CASE WHEN language = ? THEN 1 ELSE 2 END)',
                [$language]
            );
    }

    protected function mailableExists(): Attribute
    {
        return Attribute::make(get: function () {
            $className = Str::studly($this->key);
            $filePath = app_path(config('filament-email-templates.mailable_directory').sprintf('/%s.php', $className));

            return File::exists($filePath);
        });
    }

    /**
     * @throws Exception
     */
    public function getMailableClass(): string
    {
        $className = Str::studly($this->key);
        $directory = str_replace('/', '\\', config('filament-email-templates.mailable_directory', 'Mail/Maksde/FilamentEmailTemplates'));
        $fullClassName = 'App\\'.rtrim($directory, '\\').('\\'.$className);

        if (! class_exists($fullClassName)) {
            throw new RuntimeException(sprintf('Mailable class %s does not exist.', $fullClassName));
        }

        return $fullClassName;
    }

    /**
     * Gets base64 encoded content - to add to an iframe
     */
    public function getBase64EmailPreviewData(): string
    {
        $content = $this->content;

        return base64_encode($content);
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime:Y-m-d H:i:s',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'from' => 'array',
            'cc' => 'array',
            'bcc' => 'array',
        ];
    }
}
