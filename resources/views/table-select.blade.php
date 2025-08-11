@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\Facades\FilamentView;
    use Illuminate\View\ComponentAttributeBag;

    $id = $getId();
    $isMultiple = $isMultiple();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $placeholder = $getPlaceholder();

    $optionSize = $getOptionSize();
    $optionIconSize = $getOptionIconSize();

    $selectionActionName = $getSelectionActionName();
    $selectionAction = $getAction($selectionActionName);
    $selectionActionAlignment = $getSelectionActionAlignment();
    $optionExtraAttributes = \Filament\Support\prepare_inherited_attributes($getOptionExtraAttributesBag());

    if (! $selectionActionAlignment instanceof Alignment) {
        $selectionActionAlignment = Alignment::tryFrom($selectionActionAlignment) ?? $selectionActionAlignment;
    }

    $triggerSelectionActionOnInputClick = $shouldTriggerSelectionActionOnInputClick();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :inline-label-vertical-alignment="VerticalAlignment::Center"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline()"
        :inline-suffix="$isSuffixInline()"
        :prefix="$getPrefixLabel()"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$getPrefixIcon()"
        :prefix-icon-color="$getPrefixIconColor()"
        :suffix="$getSuffixLabel()"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$getSuffixIcon()"
        :suffix-icon-color="$getSuffixIconColor()"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                ->class(['py-1.5', 'px-3', 'relative', 'min-h-9'])
        "
    >
        @if ($triggerSelectionActionOnInputClick)
            <x-filament::input
                :autofocus="$isAutofocused()"
                :id="$id"
                :attributes="
                    (new ComponentAttributeBag)
                        ->class(['absolute', 'inset-0', 'opacity-0', 'z-5', 'cursor-pointer', 'min-h-9'])
                        ->merge($selectionAction->isDisabled() ? [] : [
                            'wire:click' => 'mountFormComponentAction(`' . $statePath . '`, `' . $selectionActionName . '`)',
                        ])
                "
            />
        @endif

        <div
            @if (FilamentView::hasSpaMode())
                {{-- format-ignore-start --}}ax-load="visible || event (ax-modal-opened)"{{-- format-ignore-end --}}
            @else
                ax-load
            @endif
            ax-load-src="{{ FilamentAsset::getAlpineComponentSrc('table-select', 'dvarilek/filament-table-select') }}"
            x-data="tableSelect({
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                        isMultiple: @js($isMultiple),
                    })"
            {{
                $attributes
                    ->merge([
                        'id' => $id,
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->class([
                        'grid gap-2' => $isMultiple,
                        'flex items-start gap-x-3 leading-5' => $isDisabled,
                    ])
            }}
        >
            <div
                x-cloak
                x-show="! hasSelectedOptions()"
                class="h-6 w-full text-gray-400 dark:text-gray-500"
            >
                {{ $placeholder }}
            </div>

            @if ($isMultiple && filled($optionLabels = $getOptionLabels()))
                <div
                    x-show="hasSelectedOptions()"
                    class="flex w-full flex-wrap gap-1.5"
                >
                    @foreach ($optionLabels as $optionKey => $optionLabel)
                        <x-filament::badge
                            wire:key="table-select-option-{{ $statePath }}-{{ $optionKey }}"
                            x-show="isOptionSelected('{{ $optionKey }}')"
                            class="z-10"
                            :size="$optionSize"
                            :iconSize="$optionIconSize"
                            :color="$getOptionColor($optionKey) ?? null"
                            :icon="$getOptionIcon($optionKey) ?? null"
                            :attributes="$optionExtraAttributes"
                        >
                            {{ $optionLabel }}

                            @unless ($isDisabled)
                                <x-slot
                                    name="deleteButton"
                                    x-on:click.stop.prevent="deselectOption('{{ $optionKey }}')"
                                ></x-slot>
                            @endunless
                        </x-filament::badge>
                    @endforeach
                </div>
            @elseif (filled($optionLabel = $getOptionLabel()))
                <div
                    x-show="hasSelectedOptions()"
                    class="flex h-6 w-full items-center"
                >
                    <div class="truncate">
                        {{ $optionLabel }}
                    </div>

                    @unless ($isDisabled)
                        <x-filament::icon-button
                            icon="heroicon-o-x-mark"
                            color="gray"
                            class="z-10 ml-auto"
                            x-on:mousedown.stop.prevent="deselectOption()"
                        />
                    @endunless
                </div>
            @else
                {{-- In between updates, when the state is empty, the upper placeholder doesn't show briefly which causes UI problems --}}
                <div
                    x-show="hasSelectedOptions()"
                    class="h-6 w-full text-gray-400 dark:text-gray-500"
                >
                    {{ $placeholder }}
                </div>
            @endif
        </div>
    </x-filament::input.wrapper>

    @unless ($triggerSelectionActionOnInputClick || $selectionAction->isDisabled())
        <div
            @class([
                'justify-self-start' => $selectionActionAlignment === Alignment::Start || $selectionActionAlignment === Alignment::Right,
                'justify-self-center' => $selectionActionAlignment === Alignment::Center || $selectionActionAlignment === Alignment::Between,
                'justify-self-end' => $selectionActionAlignment === Alignment::End || $selectionActionAlignment === Alignment::Left,
            ])
        >
            {{ $selectionAction }}
        </div>
    @endunless
</x-dynamic-component>
