# Filament Table Select

<div class='filament-hidden'>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dvarilek/filament-table-select.svg?include_prereleases)](https://packagist.org/packages/dvarilek/filament-table-select)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/dvarilek/filament-table-select.svg)](https://packagist.org/packages/dvarilek/filament-table-select)

</div>

<div class="filament-hidden">
    <img src="https://github.com/user-attachments/assets/80bf584a-a7bc-4586-98aa-27c377e2b6d3" alt="Filament Table Select Banner">
</div>

***
## Installation

```bash
composer require dvarilek/filament-table-select:^2.0
```

***
## Overview

This package introduces a new Form component that acts as a replacement for the Select field by allowing 
users to select related records from a full-fledged Filament table through a relationship.

Naturally, using a table for selection provides much greater context and clarity, as users can interact 
with it like any other Filament table. The table can be fully customizable.


<video controls src="https://github.com/user-attachments/assets/8497d7f7-7758-4ace-b09e-5683c1d4aea4" title="Filament Table Select Demo"></video>

<br>

***
## Getting Started

Even though TableSelect doesn't extend Select field directly, it borrows some functionality from it.

First, configure the relationship and title attribute using the `relationship()` method, which works the same way as in
Filament's standard Select field. This method sets up a `BelongsTo` relationship to automatically retrieve options, 
where the `titleAttribute` specifies the column used to generate labels for each option.

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
    ])
```


<video controls src="https://github.com/user-attachments/assets/c75141e3-320d-412a-8cd2-e3da533dcd51" title="Filament Table Select Single Selection"></video>

<br>

### Multi Selection
The `multiple()` method enables to use a `belongsToMany()` relationship, which allows to select and associate multiple
records. 

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
            ->multiple()
    ])
```

<video controls src="https://github.com/user-attachments/assets/acd35071-36d6-4f05-8533-29cf2dadea01" title="Filament Table Select Multiple Selection"></video>

<br>

### Selected Items Validation
You can validate the minimum and maximum number of items that you can select in a multi-select by 
setting the `minItems()` and `maxItems()` methods:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
            ->multiple()
            ->minItems(1)
            ->maxItems(3)
    ])
```

> [!NOTE]\
> If `maxItems(1)` is set to 1, radio-like selection gets enabled regardless of the relationship type.

<br>

***
## Customizing Selected Options

### Label Configuration 
The TableSelect field enables for customization of selected options and their badges.

To customize the color of selected options, use the `optionColor()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
            ->multiple()
            ->optionColor('success')
    ])
```

 <img src="https://github.com/user-attachments/assets/1bd8f765-d502-4e13-842a-80a56b6d3b3f" alt="Filament Table Option Color Configuration">


<br>

To customize the icon of selected options, use the `optionIcon()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
            ->multiple()
            ->optionIcon('heroicon-o-bell')
    ])
```

<img src="https://github.com/user-attachments/assets/43943a73-4de4-4ee8-89a2-db980e6cd081" alt="Filament Table Select Option Icon Configuration">


<br>

Even though both `optionColor()` and `optionIcon()` methods accept callbacks, they cannot work with the given record instance. 
This is done for performance reasons, so all selected options are not loaded into memory on each request.

To customize the colors of selected options while being able to access the Eloquent model instance, 
use the `getOptionColorFromRecordUsing()` method.

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
            ->multiple()
            ->getOptionColorFromRecordUsing(function (Client $record) {
                return match ($record->status) {
                    'lead' => 'primary',
                    'closed' => 'success',
                    'lost' => 'gray',
                    'active' => 'danger',
                    default => 'primary'
                };
            })
    ])
```

 <img src="https://github.com/user-attachments/assets/4728ca95-eda6-496c-8d8d-de25fa538a89" alt="Filament Table Select Option Color Configuration 2">

<br>

To customize the icons of selected options while being able to access the Eloquent model instance,
use the `getOptionIconFromRecordUsing()` method.

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
            ->multiple()
            ->getOptionIconFromRecordUsing(function (Client $record) {
                return match ($record->status) {
                    'lead' => 'heroicon-o-light-bulb',     
                    'closed' => 'heroicon-o-check-circle',   
                    'lost' => 'heroicon-o-x-circle',      
                    'active' => 'heroicon-o-bolt',           
                    default  => 'heroicon-o-question-mark-circle', 
                };
            })
    ])
```

<img src="https://github.com/user-attachments/assets/5d79ccd6-f198-44ec-b334-f3b13e75536c" alt="Filament Table Select Option Icon Configuration 2">

<br>

To customize the labels of selected options while being able to access the Eloquent model instance,
use the `getOptionLabelFromRecordUsing()` method.

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
            ->multiple()
            ->getOptionLabelFromRecordUsing(function (Client $record) {
                 return "{$record->first_name} {$record->last_name} - {$record->status}"
            })
    ])
```

<img src="https://github.com/user-attachments/assets/e88e1abb-234b-4e27-ae18-8d6062775a9c" alt="Filament Table Select Option Label Configuration">

<br>

### Other Configuration Options
To customize the size of selected option badges, use the `optionSize()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Filament\Support\Enums\ActionSize;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
            ->multiple()
            ->optionSize(ActionSize::Large)
    ])
```

To customize the size of selected option badges, use the `optionIconSize()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Filament\Support\Enums\IconSize;

$form
    ->schema([
        TableSelect::make('clients')
            ->relationship('clients', 'name')
            ->multiple()
            ->optionIconSize(IconSize::Large)
    ])
```


***
## Selection Table

### Selection Table Configuration
You can configure the Selection table by passing a closure into the `selectionTable()` method, this is where
you can add columns, remove actions, modify the table's query etc.

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Filament\Tables\Table;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->selectionTable(function (Table $table) {
        return $table
            ->heading('Active Clients') 
            ->actions([]) 
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'));
    })
```

Additionally, If you wish to customize the Selection Table Livewire component, you can access it as the second argument:
```php
use Dvarilek\FilamentTableSelect\Components\Livewire\SelectionTable;
use Filament\Tables\Table;

->selectionTable(function (Table $table, SelectionTable $livewire) {
    // ...
})
```
<br>

To use an already defined table from a Filament Resource, use the `tableLocation()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->tableLocation(ClientResource::class)
```

<br>


***
## Selection Action

### Selection Action Configuration
The selection action and its modal, where the table is contained, can be configured using the `selectionAction()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Filament\Forms\Components\Actions\Action;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->selectionAction(function (Action $action) {
        return $action
            ->icon('heroicon-o-user-plus') 
            ->modalHeading('Select Clients') 
            ->slideOver(false);
    })
```

<br>

### Selection Action Position
By default, the selection action is displayed in the left bottom corner. To change its position, 
use the `selectionActionAlignment()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\Alignment;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->selectionAction(function (Action $action) {
        return $action
            ->icon('heroicon-o-user-plus') 
            ->modalHeading('Select Clients') 
            ->slideOver(false);
    })
    ->selectionActionAlignment(Alignment::End)
```

Or provide an optional parameter directly in the `selectionAction()` method:
```php
use Filament\Support\Enums\Alignment;

->selectionAction(alignment: Alignment::Center)
```

<br>

### Opening Selection Modal On Click
If you with to hide this action and open the modal by clicking on the Field directly, use the
`triggerSelectionActionOnInputClick()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Filament\Forms\Components\Actions\Action;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->selectionAction(function (Action $action) {
        return $action
            ->icon('heroicon-o-user-plus') 
            ->modalHeading('Select Clients') 
            ->slideOver(false);
    })
    ->triggerSelectionActionOnInputClick() 
```

<video controls src="https://github.com/user-attachments/assets/42925915-2ec4-45d1-a202-a058b1c0c04a" title="Filament Table Select On Input"></video>


Or provide an optional parameter directly in the `selectionAction()` method:
```php
    ->selectionAction(shouldTriggerSelectionActionOnInputClick: true)
```

> [!NOTE]\
> Having this feature enabled still requires the selection action itself to be visible, because it needs to get mounted.

<br>

***
## Confirmation action


### Selection Confirmation
By default, the component's state is automatically updated as records are selected.
To require a confirmation of the selection, use the `requiresSelectionConfirmation()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->requiresSelectionConfirmation();
```

This prevents automatic state updates and adds a confirmation action to the modal. Only when this action is clicked will the form component's state get updated.


<video controls src="https://github.com/user-attachments/assets/6e47fb9c-c020-4f23-86e1-4d2c8f99f660" title="Filament Table Select Selection Confirmation"></video>

> [!IMPORTANT]\
> If you're concerned about performance, especially when updating the state would load a large number of models 
> (e.g., when using one of the getOptionFromRecord methods), consider enabling this feature.

<br>

### Closing After Selection
After confirmation, the modal closes by default. To keep it open, use the `shouldCloseAfterSelection()`:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->requiresSelectionConfirmation()
    ->shouldCloseAfterSelection(false);
```

Or provide an optional parameter directly in the `requiresSelectionConfirmation()` method:
```php
->requiresSelectionConfirmation(shouldCloseAfterSelection: false)
```

> [!NOTE]\
> Obviously, this only takes effect when selection confirmation is enabled. 

<br>

### Selection Action Position
By default, the confirmation action is positioned in the bottom left corner of the modal. To change its position use the
`confirmationActionPosition()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->requiresSelectionConfirmation() 
    ->confirmationActionPosition(SelectionModalActionPosition::TOP_LEFT);
```

Or provide an optional parameter directly in the `requiresSelectionConfirmation()` method:
```php
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;

->requiresSelectionConfirmation(confirmationActionPosition: SelectionModalActionPosition::TOP_LEFT)
```

<br>

***
## Creating New Records

### Create Option Action
In a standard Select field, if users can’t find the record they need, they can create and associate a
new one on using the `createOptionAction()`. - [Official Filament Documentation](https://filamentphp.com/docs/3.x/forms/fields/select#creating-a-new-option-in-a-modal)

The TableSelect borrows this exact functionality and displays the create option action in the selection modal.
To configure this action you can use the following methods:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->createOptionForm(ClientResource::form(...))
    ->createOptionUsing(function (array $data) {
        // Create related record using...
    })
    ->createOptionAction(function () {
        // Configure the action...
    })
```


<video controls src="https://github.com/user-attachments/assets/e8a88b39-a9e0-4d74-8ce5-4876752452d1" title="Filament Table Select Create Option Action"></video>

> [!IMPORTANT]
> When a new record is created, it's automatically selected in the table. If this newly created
> record exceeds the selection limit, the record naturally won't be selected. Obviously, in single-selection
> mode, the new record will replace the old one.

<br>

### Create Option Action Position
By default, the create option action is positioned in the top right corner of the modal. To change its position use the
`createOptionActionPosition()` method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Dvarilek\FilamentTableSelect\Enums\SelectionModalActionPosition;
use Filament\Forms\Form;

TableSelect::make('clients')
    ->relationship('clients', 'name')
    ->createOptionForm(fn (Form $form) => ClientResource::form($form))
    ->createOptionActionPosition(SelectionModalActionPosition::TOP_LEFT)
```

<br>

***
## Advanced

To globally configure all TableSelect component instances, use the `configureUsing()` method in you application's 
Service Provider boot method:

```php
use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;

public function boot(): void
{
    TableSelect::configureUsing(static function (TableSelect $tableSelect): void {
        $tableSelect->requiresSelectionConfirmation();
    });
}
```

***

## Testing

```bash
composer test
```
***

## Changelog
Please refer to [Package Releases](https://github.com/dvarilek/table-select/releases) for more information about changes.

***
## License
This package is under the MIT License. Please refer to [License File](https://github.com/dvarilek/filament-table-select/blob/main/LICENSE.md) for more information
