<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Components\Form;

use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Concerns\HasAffixes;
use Filament\Forms\Components\Concerns\HasPivotData;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Filament\Support\Concerns\HasPlaceholder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Closure;
use Exception;
use Znck\Eloquent\Relations\BelongsToThrough;

class TableSelect extends Field
{
    use Concerns\HasSelectionModalCreateOptionAction;
    use Concerns\HasSelectionAction;
    use Concerns\HasSelectionTable;
    use Concerns\HasOptionBadges;

    use HasAffixes;
    use CanLimitItemsLength;
    use HasPlaceholder;
    use HasPivotData;

    /**
     * @var view-string
     */
    protected string $view = 'filament-table-select::table-select';

    /**
     * @var view-string
     */
    protected string $selectionTableModalView = 'filament-table-select::selection-table-modal';

    protected ?Model $cachedSelectedRecord = null;

    protected bool | Closure $isMultiple = false;

    protected ?Closure $getOptionLabelUsing;

    protected ?Closure $getSelectedRecordUsing = null;

    protected ?Closure $getOptionLabelsUsing;

    protected string | Closure | null $relationshipTitleAttribute = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected string | Closure | null $relationship = null;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->default(static fn (TableSelect $component): ?array => $component->isMultiple() ? [] : null);

        $this->placeholder(static fn (TableSelect $component) => $component->isDisabled() ? null : __('filament-table-select::table-select.placeholder'));

        $this->afterStateHydrated(static function (TableSelect $component, $state): void {
            if (! $component->isMultiple()) {
                return;
            }

            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->dehydrateStateUsing(static function (TableSelect $component, mixed $state) {
            if (is_array($state) && count($state) === 1 && ! $component->isMultiple()) {
                return $state[0];
            }

            return $state;
        });

        $this->createOptionUsing(static function (TableSelect $component, array $data, Form $form) {
            $record = $component->getRelationship()->getRelated();
            $record->fill($data);
            $record->save();

            $form->model($record)->saveRelationships();

            return $record->getKey();
        });
        
        $this->registerActions([
            static fn (TableSelect $component) => $component->getSelectionAction(),
            static fn (TableSelect $component) => $component->getCreateOptionAction(),
            static function (TableSelect $component) {
                if (! $component->evaluate($component->requiresSelectionConfirmation)) {
                    return null;
                }

                return $component->getSelectionConfirmationAction();
            },
        ]);
    }

    protected function getSelectionLimit(): int
    {
        return $this->isMultiple() ? $this->getMaxItems() : 1;
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getSelectedRecordUsing(?Closure $callback): static
    {
        $this->getSelectedRecordUsing = $callback;

        return $this;
    }

    public function getOptionLabelsUsing(?Closure $callback): static
    {
        $this->getOptionLabelsUsing = $callback;

        return $this;
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function getOptionLabel(): ?string
    {
        return $this->evaluate($this->getOptionLabelUsing, [
            'value' => fn (): mixed => $this->getState(),
        ]);
    }

    /**
     * @return array<string>
     */
    public function getOptionLabels(): array
    {
        $labels = $this->evaluate($this->getOptionLabelsUsing, [
            'values' => fn (): array => $this->getState(),
        ]);

        if ($labels instanceof Arrayable) {
            $labels = $labels->toArray();
        }

        return $labels;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function relationship(string | Closure | null $name = null, string | Closure | null $titleAttribute = null, ?Closure $modifyQueryUsing = null, bool $ignoreRecord = false): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->relationshipTitleAttribute = $titleAttribute;

        $this->loadStateFromRelationshipsUsing(static function (TableSelect $component, $state) use ($modifyQueryUsing): void {
            if (filled($state)) {
                return;
            }

            $relationship = $component->getRelationship();

            if (
                ($relationship instanceof BelongsToMany) ||
                ($relationship instanceof HasOneOrManyThrough)
            ) {
                if ($modifyQueryUsing) {
                    $component->evaluate($modifyQueryUsing, [
                        'query' => $relationship->getQuery(),
                    ]);
                }

                /** @var Collection $relatedRecords */
                $relatedRecords = $relationship->getResults();

                $component->state(
                // Cast the related keys to a string, otherwise JavaScript does not
                // know how to handle deselection.
                //
                // https://github.com/filamentphp/filament/issues/1111
                    $relatedRecords
                        ->pluck(($relationship instanceof BelongsToMany) ? $relationship->getRelatedKeyName() : $relationship->getRelated()->getKeyName())
                        ->map(static fn ($key): string => strval($key))
                        ->all(),
                );

                return;
            }

            if ($relationship instanceof BelongsToThrough) {
                /** @var ?Model $relatedModel */
                $relatedModel = $relationship->getResults();

                $component->state(
                    $relatedModel?->getAttribute(
                        $relationship->getRelated()->getKeyName(),
                    ),
                );

                return;
            }

            if ($relationship instanceof HasMany) {
                /** @var Collection $relatedRecords */
                $relatedRecords = $relationship->getResults();

                $component->state(
                    $relatedRecords
                        ->pluck($relationship->getLocalKeyName())
                        ->all(),
                );

                return;
            }

            if ($relationship instanceof HasOne) {
                $relatedModel = $relationship->getResults();

                $component->state(
                    $relatedModel?->getAttribute(
                        $relationship->getLocalKeyName(),
                    ),
                );

                return;
            }

            /** @var BelongsTo $relationship */
            $relatedModel = $relationship->getResults();

            $component->state(
                $relatedModel?->getAttribute(
                    $relationship->getOwnerKeyName(),
                ),
            );
        });

        $this->getOptionLabelUsing(static function (TableSelect $component) {
            $record = $component->getSelectedRecord();

            if (! $record) {
                return null;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $component->getOptionLabelFromRecord($record);
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (str_contains($relationshipTitleAttribute, '->')) {
                $relationshipTitleAttribute = str_replace('->', '.', $relationshipTitleAttribute);
            }

            return data_get($record, $relationshipTitleAttribute);
        });

        $this->getSelectedRecordUsing(static function (TableSelect $component, $state) use ($modifyQueryUsing): ?Model {
            $relationship = Relation::noConstraints(fn () => $component->getRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            $relationshipQuery->where($component->getQualifiedRelatedKeyNameForRelationship($relationship), $state);

            if ($modifyQueryUsing) {
                $relationshipQuery = $component->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                    'search' => null,
                ]) ?? $relationshipQuery;
            }

            return $relationshipQuery->first();
        });

        $this->getOptionLabelsUsing(static function (TableSelect $component, array $values) use ($modifyQueryUsing): array {
            $relationship = Relation::noConstraints(fn () => $component->getRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            $qualifiedRelatedKeyName = $component->getQualifiedRelatedKeyNameForRelationship($relationship);

            $relationshipQuery->whereIn($qualifiedRelatedKeyName, $values);

            if ($modifyQueryUsing) {
                $relationshipQuery = $component->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                    'search' => null,
                ]) ?? $relationshipQuery;
            }

            $hasOptionLabelFromRecordUsingCallback = $component->hasOptionLabelFromRecordUsingCallback();
            $hasOptionColorFromRecordUsingCallback = $component->hasOptionColorFromRecordUsingCallback();
            $hasOptionIconFromRecordUsingCallback = $component->hasOptionIconFromRecordUsingCallback();

            if (
                $hasOptionLabelFromRecordUsingCallback ||
                $hasOptionColorFromRecordUsingCallback ||
                $hasOptionIconFromRecordUsingCallback
            ) {
                $optionLabels = [];
                $optionKeyName = Str::afterLast($qualifiedRelatedKeyName, '.');

                foreach ($relationshipQuery->get() as $record) {
                    if ($hasOptionColorFromRecordUsingCallback) {
                        $component->cacheOptionColorForRecord($record);
                    }

                    if ($hasOptionIconFromRecordUsingCallback) {
                        $component->cacheOptionIconForRecord($record);
                    }

                    if ($hasOptionLabelFromRecordUsingCallback) {
                        $optionLabels[$record->$optionKeyName] = $component->getOptionLabelFromRecord($record);
                    }
                }

                if ($hasOptionLabelFromRecordUsingCallback) {
                    return $optionLabels;
                }
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (str_contains($relationshipTitleAttribute, '->')) {
                if (! str_contains($relationshipTitleAttribute, ' as ')) {
                    $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
                }
            } else {
                $relationshipTitleAttribute = $relationshipQuery->qualifyColumn($relationshipTitleAttribute);
            }

            return $relationshipQuery
                ->pluck($relationshipTitleAttribute, $qualifiedRelatedKeyName)
                ->toArray();
        });

        $this->saveRelationshipsUsing(static function (TableSelect $component, Model $record, $state) use ($modifyQueryUsing): void {
            $relationship = $component->getRelationship();

            if (($relationship instanceof HasOne) || ($relationship instanceof HasMany)) {
                $query = $relationship->getQuery();

                if ($modifyQueryUsing) {
                    $component->evaluate($modifyQueryUsing, [
                        'query' => $query,
                        'search' => null,
                    ]);
                }

                $query->update([
                    $relationship->getForeignKeyName() => null,
                ]);

                if (! empty($state)) {
                    $relationship::noConstraints(function () use ($component, $record, $state, $modifyQueryUsing): void {
                        $relationship = $component->getRelationship();

                        $query = $relationship->getQuery()->whereIn($relationship->getLocalKeyName(), Arr::wrap($state));

                        if ($modifyQueryUsing) {
                            $component->evaluate($modifyQueryUsing, [
                                'query' => $query,
                                'search' => null,
                            ]);
                        }

                        $query->update([
                            $relationship->getForeignKeyName() => $record->getAttribute($relationship->getLocalKeyName()),
                        ]);
                    });
                }

                return;
            }

            if (
                ($relationship instanceof HasOneOrMany) ||
                ($relationship instanceof HasOneOrManyThrough) ||
                ($relationship instanceof BelongsToThrough)
            ) {
                return;
            }

            if (! $relationship instanceof BelongsToMany) {
                // If the model is new and the foreign key is already filled, we don't need to fill it again.
                // This could be a security issue if the foreign key was mutated in some way before it
                // was saved, and we don't want to overwrite that value.
                if (
                    $record->wasRecentlyCreated &&
                    filled($record->getAttributeValue($relationship->getForeignKeyName()))
                ) {
                    return;
                }

                $relationship->associate($state);
                $record->wasRecentlyCreated && $record->save();

                return;
            }

            if ($modifyQueryUsing) {
                $component->evaluate($modifyQueryUsing, [
                    'query' => $relationship->getQuery(),
                    'search' => null,
                ]);
            }

            /** @var Collection $relatedRecords */
            $relatedRecords = $relationship->getResults();

            $state = Arr::wrap($state ?? []);

            $recordsToDetach = array_diff(
                $relatedRecords
                    ->pluck($relationship->getRelatedKeyName())
                    ->map(static fn ($key): string => strval($key))
                    ->all(),
                $state,
            );

            if (count($recordsToDetach) > 0) {
                $relationship->detach($recordsToDetach);
            }

            $pivotData = $component->getPivotData();

            if ($pivotData === []) {
                $relationship->sync($state, detaching: false);

                return;
            }

            $relationship->syncWithPivotValues($state, $pivotData, detaching: false);
        });

        $this->dehydrated(fn (TableSelect $component): bool => ! $component->isMultiple());

        return $this;
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function hasOptionLabelFromRecordUsingCallback(): bool
    {
        return $this->getOptionLabelFromRecordUsing !== null;
    }

    public function getOptionLabelFromRecord(Model $record): string
    {
        return $this->evaluate(
            $this->getOptionLabelFromRecordUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );
    }

    public function getRelationshipTitleAttribute(): ?string
    {
        return $this->evaluate($this->relationshipTitleAttribute);
    }

    public function getLabel(): string | Htmlable | null
    {
        if ($this->label === null && $this->hasRelationship()) {
            $label = (string) str($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();

            return ($this->shouldTranslateLabel) ? __($label) : $label;
        }

        return parent::getLabel();
    }

    public function getRelationship(): BelongsTo | BelongsToMany | HasOneOrMany | HasOneOrManyThrough | BelongsToThrough | null
    {
        if (! $this->hasRelationship()) {
            return null;
        }

        $record = $this->getModelInstance();

        $relationship = null;

        $relationshipName = $this->getRelationshipName();

        foreach (explode('.', $relationshipName) as $nestedRelationshipName) {
            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        if (! $relationship) {
            throw new Exception("The relationship [{$relationshipName}] does not exist on the model [{$this->getModel()}].");
        }

        return $relationship;
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function getSelectedRecord(): ?Model
    {
        if ($this->cachedSelectedRecord) {
            return $this->cachedSelectedRecord;
        }

        if (blank($this->getState())) {
            return null;
        }

        return $this->cachedSelectedRecord = $this->evaluate($this->getSelectedRecordUsing);
    }

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    protected function getQualifiedRelatedKeyNameForRelationship(Relation $relationship): string
    {
        if ($relationship instanceof BelongsToMany) {
            return $relationship->getQualifiedRelatedKeyName();
        }

        if ($relationship instanceof HasOneOrManyThrough) {
            return $relationship->getQualifiedForeignKeyName();
        }

        if (
            ($relationship instanceof HasOneOrMany) ||
            ($relationship instanceof BelongsToThrough)
        ) {
            return $relationship->getRelated()->getQualifiedKeyName();
        }

        /** @var BelongsTo $relationship */

        return $relationship->getQualifiedOwnerKeyName();
    }

}
