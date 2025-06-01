<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\View\Concerns;

use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconSize;
use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasSelectBadges
{
    /**
     * @var string | ActionSize | Closure
     */
    protected string | ActionSize | Closure $selectBadgeSize = ActionSize::Medium;

    /**
     * @var string | IconSize | Closure
     */
    protected string | IconSize | Closure $selectBadgeIconSize = IconSize::Small;

    /**
     * @var null | Closure
     */
    protected ?Closure $getSelectBadgeColorUsing = null;

    /**
     * @var null | Closure
     */
    protected ?Closure $getSelectBadgeIconUsing = null;

    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraSelectBadgeAttributes = [];

    /**
     * @param string | ActionSize | Closure $size
     *
     * @return $this
     */
    public function selectBadgeSize(string | ActionSize | Closure $size): static
    {
        $this->selectBadgeSize = $size;

        return $this;
    }

    /**
     * @param  string | IconSize | Closure $size
     *
     * @return $this
     */
    public function selectBadgeIconSize(string | IconSize | Closure $size): static
    {
        $this->selectBadgeIconSize = $size;

        return $this;
    }

    /**
     * @param  null | Closure $callback
     *
     * @return $this
     */
    public function getSelectBadgeColorUsing(?Closure $callback): static
    {
        $this->getSelectBadgeColorUsing = $callback;

        return $this;
    }

    /**
     * @param  null | Closure $callback
     *
     * @return $this
     */
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

    /**
     * @return string | ActionSize
     */
    public function getSelectBadgeSize(): string | ActionSize
    {
        return $this->evaluate($this->selectBadgeSize);
    }

    /**
     * @return string | IconSize
     */
    public function getSelectBadgeIconSize(): string | IconSize
    {
        return $this->evaluate($this->selectBadgeIconSize);
    }

    /**
     * @param  mixed $optionKey
     * @param  mixed $optionValue
     *
     * @return null | string
     */
    public function getSelectBadgeColor(mixed $optionKey, mixed $optionValue): ?string
    {
        return $this->evaluate($this->getSelectBadgeColorUsing, [
            'optionKey' => $optionKey,
            'optionValue' => $optionValue,
        ]);
    }

    /**
     * @param  mixed $optionKey
     * @param  mixed $optionValue
     *
     * @return null | string
     */
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
