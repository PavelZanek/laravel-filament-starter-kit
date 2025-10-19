<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles;

use App\Filament\Admin\Resources\Roles\Pages\CreateRole;
use App\Filament\Admin\Resources\Roles\Pages\EditRole;
use App\Filament\Admin\Resources\Roles\Pages\ListRoles;
use App\Filament\Admin\Resources\Roles\Pages\ViewRole;
use App\Filament\Admin\Resources\Roles\Schemas\RoleForm;
use App\Filament\Admin\Resources\Roles\Tables\RoleTable;
use App\Filament\Traits\CommonTableColumns;
use App\Filament\Traits\CommonTableFilters;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Exception;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Override;

final class RoleResource extends Resource implements HasShieldPermissions
{
    use CommonTableColumns;
    use CommonTableFilters;
    use HasShieldFormComponents;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * @return array<int, Section>
     */
    public static function getResourceEntitiesSchema(): array
    {
        // Use FilamentShield's getResources() which properly discovers all resources
        /** @var array<int, Section> */
        return collect(FilamentShield::getResources())
            ->map(
                function (mixed $entity): Section {
                    /** @var array{resourceFqcn:string,model:string,modelFqcn:string,permissions:array<string>} $entity */
                    $resourceFqcn = $entity['resourceFqcn'];
                    $model = $entity['model'];
                    $modelFqcn = $entity['modelFqcn'];

                    $sectionLabel = self::shield()->hasLocalizedPermissionLabels()
                        ? FilamentShield::getLocalizedResourceLabel($resourceFqcn)
                        : $model;

                    return Section::make($sectionLabel)
                        ->description(fn (): HtmlString => new HtmlString('<span style="word-break: break-word;">'.Utils::showModelPath($modelFqcn).'</span>'))
                        ->compact()
                        ->schema([
                            self::getCheckBoxListComponentForResource($entity),
                        ])
                        ->columnSpan(fn (): int => (int) self::shield()->getSectionColumnSpan())
                        ->collapsible();
                })
            ->toArray();
    }

    /**
     * @return string[]
     */
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    /**
     * @throws Exception
     */
    #[Override]
    public static function table(Table $table): Table
    {
        return RoleTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getCluster(): ?string
    {
        return null; // Explicitly return null as we don't use clusters
    }

    #[Override]
    public static function getModel(): string
    {
        /** @var class-string<\Illuminate\Database\Eloquent\Model> */
        return Utils::getRoleModel();
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.role');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.roles');
    }

    #[Override]
    public static function getNavigationGroup(): string
    {
        return __('admin/user-resource.navigation_group');
    }

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('filament-shield::filament-shield.nav.role.label');
    }

    #[Override]
    public static function getNavigationIcon(): string
    {
        return __('filament-shield::filament-shield.nav.role.icon');
    }

    #[Override]
    public static function getSlug(?Panel $panel = null): string
    {
        return Utils::getResourceSlug();
    }

    /**
     * @return Builder<Role>
     */
    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Override to provide only admin panel widget permissions
     *
     * @return array<string, string>
     */
    public static function getWidgetOptions(): array
    {
        // Return only widgets that are actually used in admin panel
        return [
            'widget_UserStatsOverview' => 'User Stats Overview',
        ];
    }

    /**
     * Override to provide custom permissions (filament-panel.* permissions)
     *
     * @return array<string, string>
     */
    public static function getCustomPermissionOptions(): array
    {
        /** @var array<string, string> */
        return \Spatie\Permission\Models\Permission::where('name', 'like', 'filament-panel.%')
            ->pluck('name')
            ->mapWithKeys(function (mixed $permission): array {
                /** @var string $permission */
                $label = match ($permission) {
                    'filament-panel.admin' => 'Admin Panel Access',
                    'filament-panel.app' => 'App Panel Access',
                    'filament-panel.cms' => 'CMS Panel Access',
                    default => str($permission)->after('filament-panel.')->title()->toString().' Panel Access'
                };

                return [$permission => $label];
            })
            ->toArray();
    }
}
