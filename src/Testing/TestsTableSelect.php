<?php

namespace Dvarilek\FilamentTableSelect\Testing;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method HasForms instance()
 *
 * @mixin Testable
 */
class TestsTableSelect
{

    public function assertSelectionModalContains(): Closure
    {
        return function (string | array $content, string | array $component = 'products', string | array $name = 'tableSelectionAction', array $data = [], array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->mountFormComponentAction($component, $name, $arguments, $formName);

            /* @var Action  $selectionAction */
            $selectionAction = $this->instance()->getMountedFormComponentAction();

            foreach (Arr::wrap($content) as $needle) {
                Assert::assertStringContainsString(
                    $needle,
                    $selectionAction->getModalContent()->render(),
                    "Failed asserting that selection modal's content contains [{$needle}]."
                );
            }

            return $this;
        };
    }

    public function assertSelectionModalDoesNotContains(): Closure
    {
        return function (string | array $content, string | array $component = 'products', string | array $name = 'tableSelectionAction', array $data = [], array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->mountFormComponentAction($component, $name, $arguments, $formName);

            /* @var Action  $selectionAction */
            $selectionAction = $this->instance()->getMountedFormComponentAction();

            foreach (Arr::wrap($content) as $needle) {
                Assert::assertStringNotContainsString(
                    $needle,
                    $selectionAction->getModalContent()->render(),
                    "Failed asserting that selection modal's content does not contain [{$needle}]."
                );
            }

            return $this;
        };
    }
}