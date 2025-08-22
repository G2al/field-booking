<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TableResource\Pages;
use App\Filament\Resources\TableResource\RelationManagers;
use App\Models\Table as TableModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\CheckboxList;

class TableResource extends Resource
{
    protected static ?string $model = TableModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Campi';

    protected static ?string $modelLabel = 'Campo';

    protected static ?string $pluralModelLabel = 'Campi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome Campo')
                    ->placeholder('Es: Campo 1, Campo Centrale...')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('capacity')
                    ->label('Numero Massimo Giocatori')
                    ->required()
                    ->numeric()
                    ->minValue(5)
                    ->maxValue(22)
                    ->helperText('Es: 10 per calcetto, 22 per campo grande'),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Attivo')
                    ->default(true),
                
                Forms\Components\CheckboxList::make('opening_days')
                    ->label('Giorni di Apertura')
                    ->options([
                        '1' => 'Lunedì',
                        '2' => 'Martedì', 
                        '3' => 'Mercoledì',
                        '4' => 'Giovedì',
                        '5' => 'Venerdì',
                        '6' => 'Sabato',
                        '0' => 'Domenica',
                    ])
                    ->default(['1','2','3','4','5','6','0']) // Tutti i giorni di default
                    ->columns(3)
                    ->required(),
                    
                Forms\Components\CheckboxList::make('timeSlots')
                    ->label('Orari Disponibili')
                    ->relationship('timeSlots', 'time')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->time->format('H:i'))
                    ->columns(3)
                    ->bulkToggleable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Campo')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Giocatori')
                    ->sortable(),
                    
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Attivo/Disattivo'),

                Tables\Columns\TextColumn::make('bookings_count')  
                    ->label('Prenotazioni')
                    ->counts('bookings')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Solo Attivi'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTables::route('/'),
            'create' => Pages\CreateTable::route('/create'),
            'edit' => Pages\EditTable::route('/{record}/edit'),
        ];
    }
}
