@php
    $selectionActionName = $getSelectionActionName();
    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributes = $getExtraAttributes();
    $id = $getId();
    $isMultiple = $isMultiple();

    $isDisabled = $isDisabled();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
        :component="$fieldWrapperView"
        :field="$field"
>
    <x-filament::input.wrapper
            :disabled="$isDisabled"
            :inline-prefix="$isPrefixInline"
            :inline-suffix="$isSuffixInline"
            :prefix="$prefixLabel"
            :prefix-actions="$prefixActions"
            :prefix-icon="$prefixIcon"
            :prefix-icon-color="$getPrefixIconColor()"
            :suffix="$suffixLabel"
            :suffix-actions="$suffixActions"
            :suffix-icon="$suffixIcon"
            :suffix-icon-color="$getSuffixIconColor()"
            :valid="! $errors->has($statePath)"
            :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                ->class(['fi-fo-select'])
        "
    >
        <div
                {{
                    $attributes
                        ->merge([
                            'id' => $id,
                        ], escape: false)
                        ->merge($extraAttributes, escape: false)
                        ->class([
                            "grid gap-2" => $isMultiple,
                            "flex items-start gap-x-3 leading-5" => $isDisabled
                        ])
                }}
        >
            @if ($isMultiple)
                @if (filled($optionLabels = $getOptionLabels()))
                    <div class=" flex w-full flex-wrap gap-1.5 border-t border-t-gray-200 p-2 dark:border-t-white/10">
                        @foreach ($optionLabels as $optionLabel)
                            <x-filament::badge>
                                {{ $optionLabel }}
                            </x-filament::badge>
                        @endforeach
                    </div>
                @elseif (filled($placeholder = $getPlaceholder()))
                    <div class="text-gray-400 dark:text-gray-500">
                        {{ $placeholder }}
                    </div>
                @endif
            @else
                @if (filled($optionLabel = $getOptionLabel()))
                    {{ $optionLabel }}
                @elseif (filled($placeholder = $getPlaceholder()))
                    <div class="text-gray-400 dark:text-gray-500">
                        {{ $placeholder }}
                    </div>
                @endif
            @endif

            <div class="pt-3">
                {{ $getAction($selectionActionName) }}
            </div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
