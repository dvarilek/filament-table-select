<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Form\Concerns;

use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconSize;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

trait HasOptionBadges
{
    protected string | ActionSize | Closure $optionSize = ActionSize::Medium;

    protected string | IconSize | Closure $optionIconSize = IconSize::Small;

    protected ?Closure $getOptionColorUsing = null;

    protected ?Closure $getOptionIconUsing = null;

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

    public function getOptionColorUsing(?Closure $callback): static
    {
        $this->getOptionColorUsing = $callback;

        return $this;
    }

    public function getOptionIconUsing(?Closure $callback): static
    {
        $this->getOptionIconUsing = $callback;

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

    /**
     * @return ComponentAttributeBag
     */
    public function getOptionExtraAttributesBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getOptionExtraAttributes());
    }

    public function getOptionColorFromRecord(Model $record): ?string
    {
        return $this->evaluate($this->getOptionColorFromRecordUsing,
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
        return $this->evaluate($this->getOptionIconFromRecordUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ]
        );
    }

    public function getOptionColor(mixed $optionKey): ?string
    {
        $optionKey = (string) $optionKey;

        if ($this->hasOptionColorFromRecordUsingCallback()) {
            return $this->cachedOptionColors[$optionKey] ?? null;
        }

        return $this->evaluate($this->getOptionColorUsing, [
            'optionKey' => $optionKey
        ]);
    }

    public function getOptionIcon(mixed $optionKey): ?string
    {
        $optionKey = (string) $optionKey;

        if ($this->hasOptionIconFromRecordUsingCallback()) {
            return $this->cachedOptionIcons[$optionKey] ?? null;
        }

        return $this->evaluate($this->getOptionIconUsing, [
            'optionKey' => $optionKey,
        ]);
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
}
