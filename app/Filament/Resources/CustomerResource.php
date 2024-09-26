<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Cadastros';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?string $slug = 'cliente';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('document')
                    ->label('CPF/CNPJ')
                    // ->mask('99.999.999/9999-99')
                    ->required()
                    ->suffixAction(
                        Action::make('search')
                            ->icon('heroicon-o-magnifying-glass')
                            ->action(function(Set $set, $state){
                                if (blank($state)) {
                                    Notification::make()
                                        ->title('Digite um CNPJ')
                                        ->danger()->send();
                                        return;
                                }
                                try {
                                    $data = Http::get("https://www.receitaws.com.br/v1/cnpj/".$state)
                                        ->throw()->json();

                                        if(!empty($data['status'] == 'ERROR')) {
                                            Notification::make()
                                            ->title($data['message'])
                                            ->danger()->send();
                                        }
                                } catch (Exception $e) {
                                    Notification::make()
                                        ->title('CNPJ não encontrado')
                                        ->danger()->send();
                                }
                                $set('name', $data['nome'] ?? null);
                                $set('email', $data['email'] ?? null);
                                $set('phone', $data['telefone'] ?? null);
                                $set('zipcode', $data['cep'] ?? null);
                                $set('street', $data['logradouro'] ?? null);
                                $set('number', $data['numero'] ?? null);
                                $set('complement', $data['complemento'] ?? null);
                                $set('neighborhood', $data['bairro'] ?? null);
                                $set('city', $data['municipio'] ?? null);
                                $set('state', $data['uf'] ?? null);
                            })
                        )
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefone')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('zipcode')
                    ->label('CEP')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('street')
                    ->label('Endereço')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('number')
                    ->label('Número')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('complement')
                    ->label('Complemento')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('neighborhood')
                    ->label('Bairro')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->label('Cidade')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('state')
                    ->label('Estado')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('status')
                    ->label('Ativo')
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document')
                    ->label('CPF/CNPJ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('NOME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                ->label('TELEFONE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zipcode')
                    ->label('CEP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('street')
                    ->label('ENDEREÇO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('NÚMERO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('complement')
                    ->label('COMPLEMENTO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('neighborhood')
                    ->label('BAIRRO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('CIDADE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->label('ESTADO')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCustomers::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
