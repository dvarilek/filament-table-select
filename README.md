# Filament Table Select

This package introduces a Table Select component that replaces Filament's Select component and 
allows to select related records from a full-fledged Filament table. This widens the selection context 
and offers a significantly better user experience.

> [!CAUTION]
> This package is currently in its early stages of development.


// TODO: Add Images

***
## Installation

```bash
composer require dvarilek/filament-table-select
```

Additionally, you can publish the translation files:
```bash
php artisan vendor:publish --tag=filament-table-select
```

This package introduces a Table Select component that replaces 

***
## Getting Started

No configuration is required! Simply include the component in your form schema:

```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;

$form
    ->schema([
        TableSelect::make('products')
            ->relationship('products', 'name')
    ])
```

> [!NOTE]\
> The TableSelect component extends Filament's select component, meaning all of selects methods are available.

***
## Overview

TODO: Documentation section headings are in progress - change them.

The Table Select component extends the regular Filament Select component, meaning all of its original methods 
are still available. Users can still operate it like a standard select by choosing records from a dropdown.

What makes this component special is the selection suffix action. When clicked, this action opens a modal 
containing a Selection Table. This table is used to select related records by either clicking the record 
checkboxes or the entire row. Users can interact with the table just like any other Filament 
table - searching, filtering, grouping, and more - enabling them to efficiently locate the records they need.

The Selection Table respects the component's option limit and selection mode (singular/multiple).
When the selection limit is reached, all remaining options are automatically disabled.
In single-selection mode, the table acts similarly to a radio for a better selection experience.

## Configuring the Selection Table

You can configure the selection table using 'modifySelectionTable' method:
```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
use Filament\Tables\Table;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->modifySelectionTable(function (Table $table) {
        return $table
            ->heading('Select Available Products') 
            ->actions([]) 
            ->modifyQueryUsing(fn (Builder $query) => $query->where('is_available', true)) 
    })
```

To use an already defined table in a Filament Resource, use the 'tableLocation' method:
```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->tableLocation(ProductResource::class)
```

// TODO: Mention disableWhen option when implemented.

## Selection Action
The selection action and its modal where the table is hosted can be configured using the modifySelectionAction method:
```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
use Filament\Forms\Components\Actions\Action;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->modifySelectionAction(function (Action $action) {
        return $action
            ->icon('heroicon-o-box')
            ->modalHeading('Select related products')
            ->slideOver(false)
    })
```

## Confirmation action

By default, the component's state is automatically updated as records are selected.
To require a confirmation of the selection, use the 'requiresSelectionConfirmation' method:
```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->requiresSelectionConfirmation();
```

This prevents automatic state updates and adds a confirmation action to the modal. Only when this action is clicked will the component's state be updated with the selected records.

After confirmation, the modal closes by default. To keep it open, use:
```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->requiresSelectionConfirmation()
    ->shouldCloseOnSelection(false);
```

Or provide an optional parameter directly in the 'requiresSelectionConfirmation' method:
```php
    ->requiresSelectionConfirmation(shouldCloseOnSelection: false)
```


By default, the confirmation action is positioned in the bottom left corner of the modal. To change its position use the
'confirmationActionPosition' method:
```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->requiresSelectionConfirmation() 
    ->confirmationActionPosition(SelectionModalActionPosition::TOP_LEFT);
```
Or provide an optional parameter directly in the 'requiresSelectionConfirmation' method:
```php
    ->requiresSelectionConfirmation(confirmationActionPosition: SelectionModalActionPosition::TOP_LEFT)
```

## Creating New Records

If users can't find the record they need, they can create it directly within the modal without needing to close it:

```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->hasCreateOptionActionInSelectionModal()
```

The create option action in the modal is a direct clone of Filament's Select create option action. This means, that it 
works and can be configured as the regular create option action. [Official Filament Documentation](https://filamentphp.com/docs/3.x/forms/fields/select#creating-a-new-option-in-a-modal)

> [!IMPORTANT]
> The Selection modal create option action can only be used when the Table Select component
> defines its own create option action.

```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->createOptionForm([
        // Configure Form schema...
    ])
    ->createOptionUsing(function (array $data) {
        // Create related record using...
    })
    ->createOptionAction(function () {
        // Configure the action...
    })
```

When a new record is created, it's automatically selected in the table. If this newly created
record exceeds the selection limit, the record naturally won't be selected. However, in single-selection
mode, the new record will replace the old one.

To avoid confusion, the standard suffix create option action is hidden by default when using the selection modal version. 
To display both use the 'createOptionActionOnlyVisibleInSelectionModal' method:

```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Filament\Forms\Form;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->createOptionForm(fn (Form $form) => ProductResource::form($form))
    ->hasCreateOptionActionInSelectionModal()
    ->createOptionActionOnlyVisibleInSelectionModal(false)
```

Or provide an optional parameter directly in the 'hasCreateOptionActionInSelectionModal' method:
```php
    ->hasCreateOptionActionInSelectionModal(createOptionActionOnlyVisibleInSelectionModal: false)
```

The action will now get displayed in the form component and the selection modal.

By default, the create option action is positioned in the top right corner of the modal. To change its position use the
'selectionModalCreateOptionActionPosition' method:

```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Filament\Forms\Form;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->createOptionForm(fn (Form $form) => ProductResource::form($form))
    ->hasCreateOptionActionInSelectionModal()
    ->selectionModalCreateOptionActionPosition(SelectionModalActionPosition::TOP_LEFT)
```

Or provide an optional parameter directly in the 'hasCreateOptionActionInSelectionModal' method:
```php
    ->hasCreateOptionActionInSelectionModal(selectionModalCreateOptionActionPosition: SelectionModalActionPosition::TOP_LEFT)
```

To customize only the modal's create action without affecting the original create option action, use 
'modifySelectionModalCreateOptionAction' method:
```php
use Dvarilek\FilamentTableSelect\Components\View\TableSelect;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Filament\Forms\Form;
use Filament\Forms\Components\Actions\Action;

TableSelect::make('products')
    ->relationship('products', 'name')
    ->createOptionForm(fn (Form $form) => ProductResource::form($form))
    ->hasCreateOptionActionInSelectionModal()
    ->modifySelectionModalCreateOptionAction(function (Action $action) {
        return $action->label('Add new product');
    })
```

***

## Testing

```bash
composer test && composer stan
```
***

## Changelog
Please refer to [Package Releases](https://github.com/dvarilek/table-select/releases) for more information about changes.

***
## License
This package is under the MIT License. Please refer to [License File](LICENSE.md) for more information
