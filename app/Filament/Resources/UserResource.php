<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Archilex\AdvancedTables\Filters\Operators\NumericOperator;
use Archilex\AdvancedTables\Filters\Operators\TextOperator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Archilex\AdvancedTables\Concerns\HasViews;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Archilex\AdvancedTables\Filters\AdvancedFilter;
use Archilex\FilamentFilterSets\Filters\FilterSetFilter;
use Archilex\AdvancedTables\Filters\NumericFilter;
use Archilex\AdvancedTables\Filters\TextFilter;
use Archilex\AdvancedTables\Filters\SelectFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class UserResource extends Resource
{
    use AdvancedTables;

    use HasViews;

protected static ?string $model = User::class;

protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->required()->email(),
                FileUpload::make('image'),
                TextInput::make('password')
                    ->password()
                    ->visibleOn('create')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\CheckboxColumn::make('is_active')->label('Status'),
                Tables\Columns\TextColumn::make('sponsor.email')->label('Parent'),
                Tables\Columns\TextColumn::make('wallet_balance')->label('Balance')->numeric(),
                Tables\Columns\TextColumn::make('created_at')->date()->label('Created At')

            ])
            ->filters([
                Filter::make('is_active')
                    ->query(fn(Builder $query): Builder => $query->where('is_active', true))
            ->toggle(),
          TextFilter::make('sponsor.email')
              ->includeOperators([
                  TextOperator::IS
              ]),
        NumericFilter::make('wallet_balance')
            ->includeOperators([
                NumericOperator::GREATER_THAN
            ]),
        SelectFilter::make('sponsor.email')
            ->options([
                'testing1@eazybot.com' => 134481
            ])
            ->multiple(),
        AdvancedFilter::make()->excludeColumns([
            'is_active',
        ]),
        AdvancedFilter::make()->includeColumns([
             'email','name', 'sponsor.email', 'wallet_balance', 'created_at'
        ]),
            ])
            ->actions([
        Tables\Actions\EditAction::make(),
        Action::make('delete')
            ->requiresConfirmation()
            ->action(fn(User $record) => $record->delete())
            ])
            ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
            Tables\Actions\DeleteBulkAction::make(),
            ExportBulkAction::make()

        ]),
    ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}