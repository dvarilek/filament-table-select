@php
    $selectionActionName = $getSelectionActionName();
    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributes = $getExtraAttributes();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isMultiple = $isMultiple();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
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
                <div class="flex flex-wrap gap-1.5">
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

        @unless ($isDisabled)
            <div class="pt-3">
                {{ $getAction($selectionActionName) }}
            </div>
        @endunless
    </div>
</x-dynamic-component>