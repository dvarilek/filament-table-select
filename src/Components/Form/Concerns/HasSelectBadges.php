<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Form\Concerns;

use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconSize;
use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasSelectBadges
{
    protected string | ActionSize | Closure $selectBadgeSize = ActionSize::Medium;

    protected string | IconSize | Closure $selectBadgeIconSize = IconSize::Small;

    protected ?Closure $getSelectBadgeColorUsing = null;

    protected ?Closure $getSelectBadgeIconUsing = null;

    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraSelectBadgeAttributes = [];

    public function selectBadgeSize(string | ActionSize | Closure $size): static
    {
        $this->selectBadgeSize = $size;

        return $this;
    }

    public function selectBadgeIconSize(string | IconSize | Closure $size): static
    {
        $this->selectBadgeIconSize = $size;

        return $this;
    }

    public function getSelectBadgeColorUsing(?Closure $callback): static
    {
        $this->getSelectBadgeColorUsing = $callback;

        return $this;
    }

    public function getSelectBadgeIconUsing(?Closure $callback): static
    {
        $this->getSelectBadgeIconUsing = $callback;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraSelectBadgeAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraSelectBadgeAttributes[] = $attributes;
        } else {
            $this->extraSelectBadgeAttributes = [$attributes];
        }

        return $this;
    }

    public function getSelectBadgeSize(): string | ActionSize
    {
        return $this->evaluate($this->selectBadgeSize);
    }

    public function getSelectBadgeIconSize(): string | IconSize
    {
        return $this->evaluate($this->selectBadgeIconSize);
    }

    public function getSelectBadgeColor(mixed $optionKey, mixed $optionValue): ?string
    {
        return $this->evaluate($this->getSelectBadgeColorUsing, [
            'optionKey' => $optionKey,
            'optionValue' => $optionValue,
        ]);
    }

    public function getSelectBadgeIcon(mixed $optionKey, mixed $optionValue): ?string
    {
        return $this->evaluate($this->getSelectBadgeIconUsing, [
            'optionKey' => $optionKey,
            'optionValue' => $optionValue,
        ]);
    }

    /**
     * @return array<mixed>
     */
    public function getExtraSelectBadgeAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraSelectBadgeAttributes as $extraAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    /**
     * @return ComponentAttributeBag
     */
    public function getExtraSelectBadgeAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraSelectBadgeAttributes());
    }
}
