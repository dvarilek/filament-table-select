@php
    use Filament\Support\Enums\Alignment;

    $id = $getId();
    $isMultiple = $isMultiple();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $placeholder = $getPlaceholder();

    $selectBadgeSize = $getSelectBadgeSize();
    $selectBadgeIconSize = $getSelectBadgeIconSize();

    $selectionActionName = $getSelectionActionName();
    $selectionAction = $getAction($selectionActionName);
    $selectionActionAlignment = $getSelectionActionAlignment();
    $extraSelectBadgeAttributes = \Filament\Support\prepare_inherited_attributes($getExtraSelectBadgeAttributeBag());

    if (! $selectionActionAlignment instanceof Alignment) {
        $selectionActionAlignment = Alignment::tryFrom($selectionActionAlignment) ?? $selectionActionAlignment;
    }

    $triggerSelectionActionOnInputClick = $shouldTriggerSelectionActionOnInputClick();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
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
        @if($triggerSelectionActionOnInputClick)
            <x-filament::input
                :autofocus="$isAutofocused()"
                :id="$id"
                :attributes="
                    (new \Illuminate\View\ComponentAttributeBag)
                        ->class(['absolute', 'inset-0', 'opacity-0', 'z-5', 'cursor-pointer', 'min-h-9'])
                        ->merge($selectionAction->isDisabled() ? [] : [
                            'wire:click' => 'mountFormComponentAction(`' . $statePath . '`, `' . $selectionActionName . '`)'
                        ])
                "
            />
        @endif

        <div
            x-data="{
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},

                deleteOption: function (optionToDelete = null) {
                    if (optionToDelete === null) {
                        this.state = null;

                        $refs[`table-select-badge-{{ $id }}-single`].remove();

                        return;
                    }

                    this.state = this.state?.filter(key => key !== optionToDelete);

                    {{-- Since the only way a new item can be added is through the selection table which refreshes the field, it can just simply be removed without the state being live --}}
                    $refs[`table-select-badge-{{ $id }}-${optionToDelete}`].remove();
                },
            }"
            {{
                $attributes
                    ->merge([
                        'id' => $id
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->class([
                        "grid gap-2" => $isMultiple,
                        "flex items-start gap-x-3 leading-5" => $isDisabled
                    ])
            }}
        >
            @if ($isMultiple)
                @if (filled($optionLabels = $getOptionLabels()))
                    <div class="flex w-full flex-wrap gap-1.5">
                        <template x-if="state.length === 0">
                            <div class="text-gray-400 dark:text-gray-500 h-6">
                                {{ $placeholder }}
                            </div>
                        </template>

                        @foreach ($optionLabels as $optionKey => $optionLabel)
                            <x-filament::badge
                                x-ref="table-select-badge-{{ $id }}-{{ $optionKey }}"
                                class="z-10"
                                :size="$selectBadgeSize"
                                :iconSize="$selectBadgeIconSize"
                                :color="$getSelectBadgeColor($optionKey, $optionLabel) ?? null"
                                :icon="$getSelectBadgeIcon($optionKey, $optionLabel) ?? null"
                                :attributes="$extraSelectBadgeAttributes"
                            >
                                {{ $optionLabel }}

                                @unless($isDisabled)
                                    <x-slot
                                        name="deleteButton"
                                        x-on:click.stop="deleteOption('{{ $optionKey }}')"
                                    ></x-slot>
                                @endunless
                            </x-filament::badge>
                        @endforeach
                    </div>
                @else
                    <div class="text-gray-400 dark:text-gray-500 h-6">
                        {{ $placeholder }}
                    </div>
                @endif
            @elseif (filled($optionLabel = $getOptionLabel()))
                <div class="flex w-full items-center">
                    <template x-if="state === null">
                        <div class="text-gray-400 dark:text-gray-500 w-full h-6">
                            {{ $placeholder }}
                        </div>
                    </template>

                    <div
                        x-ref="table-select-badge-{{ $id }}-single"
                    >
                        {{ $optionLabel }}
                    </div>

                    @unless($isDisabled)
                        <x-filament::icon-button
                            icon="heroicon-o-x-mark"
                            color="gray"
                            class="ml-auto z-10"
                            x-on:click="deleteOption()"
                        />
                    @endunless
                </div>
            @else
                <div class="text-gray-400 dark:text-gray-500 h-6">
                    {{ $placeholder }}
                </div>
            @endif
        </div>
    </x-filament::input.wrapper>

    @unless ($triggerSelectionActionOnInputClick)
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
