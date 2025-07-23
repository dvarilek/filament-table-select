<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Form\Concerns;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconSize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

/**
 * @mixin Field
 */
trait HasOptionBadges
{
    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | bool | Closure | null
     */
    protected string | array | bool | Closure | null $optionColor = null;

    protected ?string $optionIcon = null;

    protected string | ActionSize | Closure $optionSize = ActionSize::Medium;

    protected string | IconSize | Closure $optionIconSize = IconSize::Small;

    protected ?Closure $getOptionColorFromRecordUsing = null;

    protected ?Closure $getOptionIconFromRecordUsing = null;

    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $optionExtraAttributes = [];

    /**
     * @var array<mixed, string>
     */
    protected array $cachedOptionColors = [];

    /**
     * @var array<mixed, string>
     */
    protected array $cachedOptionIcons = [];

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | bool | Closure | null  $optionColor
     */
    public function optionColor(string | array | bool | Closure | null $optionColor): static
    {
        $this->optionColor = $optionColor;

        return $this;
    }

    public function optionIcon(?string $optionIcon): static
    {
        $this->optionIcon = $optionIcon;

        return $this;
    }

    public function optionSize(string | ActionSize | Closure $size): static
    {
        $this->optionSize = $size;

        return $this;
    }

    public function optionIconSize(string | IconSize | Closure $size): static
    {
        $this->optionIconSize = $size;

        return $this;
    }

    public function getOptionColorFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionColorFromRecordUsing = $callback;

        return $this;
    }

    public function getOptionIconFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionIconFromRecordUsing = $callback;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function optionExtraAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->optionExtraAttributes[] = $attributes;
        } else {
            $this->optionExtraAttributes = [$attributes];
        }

        return $this;
    }

    public function getOptionSize(): string | ActionSize
    {
        return $this->evaluate($this->optionSize);
    }

    public function getOptionIconSize(): string | IconSize
    {
        return $this->evaluate($this->optionIconSize);
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | bool | Closure | null
     */
    public function getOptionColor(mixed $optionKey): string | array | bool | Closure | null
    {
        $optionKey = (string) $optionKey;

        if ($this->hasOptionColorFromRecordUsingCallback()) {
            return $this->cachedOptionColors[$optionKey] ?? null;
        }

        return $this->evaluate($this->optionColor, [
            'optionKey' => $optionKey,
        ]);
    }

    public function getOptionIcon(mixed $optionKey): ?string
    {
        $optionKey = (string) $optionKey;

        if ($this->hasOptionIconFromRecordUsingCallback()) {
            return $this->cachedOptionIcons[$optionKey] ?? null;
        }

        return $this->evaluate($this->optionIcon, [
            'optionKey' => $optionKey,
        ]);
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | bool | Closure | null
     */
    public function getOptionColorFromRecord(Model $record): string | array | bool | Closure | null
    {
        return $this->evaluate(
            $this->getOptionColorFromRecordUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ]
        );
    }

    public function getOptionIconFromRecord(Model $record): ?string
    {
        return $this->evaluate(
            $this->getOptionIconFromRecordUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ]
        );
    }

    public function hasOptionColorFromRecordUsingCallback(): bool
    {
        return $this->getOptionColorFromRecordUsing !== null;
    }

    public function hasOptionIconFromRecordUsingCallback(): bool
    {
        return $this->getOptionIconFromRecordUsing !== null;
    }

    public function cacheOptionColorForRecord(Model $record): void
    {
        $this->cachedOptionColors[(string) $record->getKey()] = $this->getOptionColorFromRecord($record);
    }

    public function cacheOptionIconForRecord(Model $record): void
    {
        $this->cachedOptionIcons[(string) $record->getKey()] = $this->getOptionIconFromRecord($record);
    }

    /**
     * @return array<mixed>
     */
    public function getOptionExtraAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->optionExtraAttributes as $extraAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getOptionExtraAttributesBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getOptionExtraAttributes());
    }
}
