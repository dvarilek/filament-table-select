<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Form\Concerns;

use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Closure;
use Filament\Forms\Form;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Js;

/**
 * @mixin Field
 */
trait HasSelectionModalCreateOptionAction
{
    protected ?Closure $modifyCreateOptionActionUsing = null;

    /**
     * @var array<Component> | Closure | null
     */
    protected array | Closure | null $createOptionActionForm = null;

    protected ?Closure $createOptionUsing = null;

    protected string | Closure | null $createOptionModalHeading = null;

    protected SelectionModalActionPosition | Closure $createOptionActionPosition = SelectionModalActionPosition::TOP_RIGHT;

    public function createOptionAction(?Closure $callback): static
    {
        $this->modifyCreateOptionActionUsing = $callback;

        return $this;
    }

    /**
     * @param  array<Component> | Closure | null  $schema
     */
    public function createOptionForm(array | Closure | null $schema): static
    {
        $this->createOptionActionForm = $schema;

        return $this;
    }

    public function createOptionUsing(?Closure $callback): static
    {
        $this->createOptionUsing = $callback;

        return $this;
    }

    public function getCreateOptionUsing(): ?Closure
    {
        return $this->createOptionUsing;
    }
    public function createOptionModalHeading(string | Closure | null $heading): static
    {
        $this->createOptionModalHeading = $heading;

        return $this;
    }

    public function getCreateOptionActionForm(Form $form): array | Form | null
    {
        return $this->evaluate($this->createOptionActionForm, ['form' => $form]);
    }

    public function hasCreateOptionActionFormSchema(): bool
    {
        return (bool) $this->createOptionActionForm;
    }

    public function createOptionActionPosition(SelectionModalActionPosition | Closure $createOptionActionPosition): static
    {
        $this->createOptionActionPosition = $createOptionActionPosition;

        return $this;
    }

    public function getCreateOptionAction(): ?Action
    {
        if ($this->isDisabled()) {
            return null;
        }

        if (! $this->hasCreateOptionActionFormSchema()) {
            return null;
        }

        $action = Action::make($this->getSelectionModalCreateOptionActionName())
            ->label(__('filament-table-select::table-select.actions.selection-create-option.label'))
            ->modalHeading(__('filament-table-select::table-select.actions.selection-create-option.label'))
            ->button()
            ->form(function (TableSelect $component, Form $form): array | Form | null {
                return $component->getCreateOptionActionForm($form->model(
                    $component->getRelationship() ? $component->getRelationship()->getModel()::class : null,
                ));
            })
            ->action(static function (Action $action, array $arguments, TableSelect $component, array $data, ComponentContainer $form) {
                if (!$component->getCreateOptionUsing()) {
                    throw new \Exception("Select field [{$component->getStatePath()}] must have a [createOptionUsing()] closure set.");
                }

                // If the key is not strvalled, 'selectedRecords' won't be treated properly by Filament Table.
                $createdOptionKey = strval($component->evaluate($component->getCreateOptionUsing(), [
                    'data' => $data,
                    'form' => $form,
                ]));

                $state = $component->isMultiple()
                    ? [
                        ...$component->getState(),
                        $createdOptionKey,
                    ]
                    : $createdOptionKey;

                $selectionLimit = $component->getSelectionLimit();

                if ($component->evaluate($component->requiresSelectionConfirmation)) {
                    $statePath = Js::from($component->getStatePath());
                    $createdOptionKey = Js::from($createdOptionKey);
                    $selectionLimit = Js::from($selectionLimit);

                    $component->getLivewire()->js(<<<JS
                        if ($selectionLimit === 1) {
                            Alpine.store('selectionModalCache').set($statePath, [$createdOptionKey]);

                            return;
                        }

                        if ($selectionLimit !== null && Alpine.store('selectionModalCache').get($statePath)?.length >= $selectionLimit) {
                            return;
                        }

                        Alpine.store('selectionModalCache').push($statePath, $createdOptionKey);
                    JS);
                } elseif ($selectionLimit === 1 || $selectionLimit === null || $selectionLimit >= count($state)) {
                    $jsonState = Js::from(is_array($state) ? $state : [$state]);
                    $statePath = Js::from($component->getStatePath());

                    // Setting the component's state normally doesn't work on single selection
                    $component->getLivewire()->js(<<<JS
                        Alpine.store('selectionModalCache').set($statePath, $jsonState);
        
                        \$nextTick(() => \$wire.set($statePath, $jsonState));
                    JS);
                }

                if (! ($arguments['another'] ?? false)) {
                    return;
                }

                $action->callAfter();

                $form->fill();
                $action->halt();
            })
            ->color('gray')
            ->icon(FilamentIcon::resolve('forms::components.select.actions.create-option') ?? 'heroicon-m-plus')
            ->modalSubmitActionLabel(__('filament-forms::components.select.actions.create_option.modal.actions.create.label'))
            ->extraModalFooterActions(fn (Action $action, TableSelect $component): array => $component->isMultiple() ? [
                $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                    ->label(__('filament-forms::components.select.actions.create_option.modal.actions.create_another.label')),
            ] : []);

        if ($this->modifyCreateOptionActionUsing) {
            $action = $this->evaluate($this->modifyCreateOptionActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function getSelectionModalCreateOptionActionName(): string
    {
        return 'selectionModalCreateOptionAction';
    }
}
