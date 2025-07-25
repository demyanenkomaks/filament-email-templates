<?php

namespace Maksde\FilamentEmailTemplates\Resources;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Maksde\FilamentEmailTemplates\Contracts\CreateMailableInterface;
use Maksde\FilamentEmailTemplates\FilamentEmailTemplatesPlugin;
use Maksde\FilamentEmailTemplates\Models\EmailTemplate;
use Maksde\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;
use Schmeits\FilamentCharacterCounter\Forms\Components\Textarea;
use Schmeits\FilamentCharacterCounter\Forms\Components\TextInput;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static bool $hasTitleCaseModelLabel = false;

    public static function shouldRegisterNavigation(): bool
    {
        return (new FilamentEmailTemplatesPlugin)->shouldRegisterNavigation();
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-email-templates.navigation.templates.icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return (new FilamentEmailTemplatesPlugin)->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-email-templates.navigation.templates.sort');
    }

    public static function getModelLabel(): string
    {
        return __(config('filament-email-templates.navigation.templates.label'));
    }

    public static function getPluralModelLabel(): string
    {
        return __(config('filament-email-templates.navigation.templates.label'));
    }

    public static function getCluster(): string
    {
        return config('filament-email-templates.navigation.templates.cluster');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return config('filament-email-templates.navigation.templates.position');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(EmailTemplate::query())
            ->columns(
                [
                    TextColumn::make('id')
                        ->sortable()
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('key')
                        ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.key'))
                        ->limit(50)
                        ->sortable()
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('name')
                        ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.template-name'))
                        ->limit(50)
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('language')
                        ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.language'))
                        ->limit(50),
                    TextColumn::make('subject')
                        ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.subject'))
                        ->searchable()
                        ->limit(50)
                        ->toggleable(),
                    TextColumn::make('from.email')
                        ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.email-from'))
                        ->searchable()
                        ->limit(50)
                        ->toggleable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('from.name')
                        ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.email-from-name'))
                        ->searchable()
                        ->limit(50)
                        ->toggleable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]
            )
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('language')
                    ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.language'))
                    ->options(config('filament-email-templates.languages')),
            ])
            ->actions([
                Action::make('create-mail-class')
                    ->label('Build Class')
                    ->visible(function (EmailTemplate $record) {
                        return ! $record->mailable_exists; // @phpstan-ignore property.notFound
                    })
                    ->icon('heroicon-o-document-text')
                    ->action(function (EmailTemplate $record): void {
                        $notify = app(CreateMailableInterface::class)->createMailable($record);
                        Notification::make()
                            ->title($notify->title)
                            ->icon($notify->icon)
                            ->iconColor($notify->icon_color)
                            ->duration(10000)
                            ->body("<span style='overflow-wrap: anywhere;'>".$notify->body.'</span>')
                            ->send();
                    }),
                Tables\Actions\ViewAction::make('Preview')
                    ->icon('heroicon-o-magnifying-glass')
                    ->modalContent(fn (EmailTemplate $record): View => view(
                        'md-filament-email-templates::forms.components.iframe',
                        ['record' => $record],
                    ))->form(null)
                    ->modalHeading(fn (EmailTemplate $record): string => __('md-filament-email-templates::filament-email-templates.form-fields-labels.preview-email').': '.$record->name)
                    ->modalDescription(fn (EmailTemplate $record): string => __('md-filament-email-templates::filament-email-templates.form-fields-labels.subject').': '.$record->subject)
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->slideOver(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    Grid::make(['default' => 1])
                        ->schema([
                            TextInput::make('name')
                                ->live()
                                ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.template-name'))
                                ->hint(__('md-filament-email-templates::filament-email-templates.form-fields-labels.template-name-hint'))
                                ->maxLength(255)
                                ->required(),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 1, 'md' => 2])
                        ->schema([
                            TextInput::make('key')
                                ->afterStateUpdated(
                                    fn (Set $set, ?string $state): mixed => $set('key', Str::slug($state))
                                )
                                ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.key'))
                                ->hint(__('md-filament-email-templates::filament-email-templates.form-fields-labels.key-hint'))
                                ->required()
                                ->unique(table: EmailTemplate::class,
                                    column: 'key',
                                    ignoreRecord: true,
                                    modifyRuleUsing: function (Unique $rule, $get) {
                                        return $rule->where('language', $get('language'));
                                    })
                                ->maxLength(255),
                            Select::make('language')
                                ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.language'))
                                ->options(config('filament-email-templates.languages'))
                                ->default(config('filament-email-templates.default_locale'))
                                ->searchable()
                                ->allowHtml(),
                            TextInput::make('from.email')->default(config('mail.from.address'))
                                ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.email-from'))
                                ->email()
                                ->maxLength(255),
                            TextInput::make('from.name')->default(config('mail.from.name'))
                                ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.email-from-name'))
                                ->string()
                                ->maxLength(255),
                        ]),

                    Grid::make(['default' => 1])
                        ->schema([
                            TextInput::make('subject')
                                ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.subject'))
                                ->maxLength(255),

                            Textarea::make('content')
                                ->label(__('md-filament-email-templates::filament-email-templates.form-fields-labels.content'))
                                ->rows(10)
                                ->autosize()
                                ->maxLength(50000),
                        ]),

                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
