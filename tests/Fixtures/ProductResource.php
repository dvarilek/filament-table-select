<?php

namespace Dvarilek\FilamentTableSelect\Tests\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;

// Support both Filament 3 (Form) and Filament 5 (Schema)
if (class_exists(\Filament\Schemas\Schema::class)) {
    class_alias(\Filament\Schemas\Schema::class, 'Dvarilek\FilamentTableSelect\Tests\Fixtures\ResourceFormOrSchema');
} else {
    class_alias(\Filament\Forms\Form::class, 'Dvarilek\FilamentTableSelect\Tests\Fixtures\ResourceFormOrSchema');
}

class ProductResource extends Resource
{
    public static function form(ResourceFormOrSchema $form): ResourceFormOrSchema
    {
        return $form
            ->schema([
                TextInput::make('name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Custom Description From Resource');
    }
}
