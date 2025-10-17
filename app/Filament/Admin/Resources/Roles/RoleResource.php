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
     * @return array<Section>
     */
    public static function getResourceEntitiesSchema(): array
    {
        // Get all resources from all panels and map them to correct permission names
        $resourceEntities = self::getAllResourcesFromAllPanels()
            ->sortKeys()
            ->map(
                function (array $entity): Section { // @phpstan-ignore argument.type
                    /** @var array{resource:string,model:string,fqcn:string} $entity */
                    $sectionLabel = $entity['model'];

                    return Section::make($sectionLabel)
                        ->description(fn (): HtmlString => new HtmlString('<span style="word-break: break-word;">'.Utils::showModelPath($entity['fqcn']).'</span>'))
                        ->compact()
                        ->schema([
                            static::getCheckBoxListComponentForResource($entity),
                        ])
                        ->columnSpan(fn (): int => (int) static::shield()->getSectionColumnSpan())
                        ->collapsible();
                });

        // Return all resource entities (custom entities are now included in getAllResourcesFromAllPanels)
        /** @var array<Section> */
        return $resourceEntities->toArray();
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

//    #[Override]
//    public static function shouldRegisterNavigation(): bool
//    {
//        return Utils::isResourceNavigationRegistered();
//    }

//    #[Override]
//    public static function getNavigationGroup(): string
//    {
//        return Utils::isResourceNavigationGroupEnabled()
//            ? __('filament-shield::filament-shield.nav.group')
//            : '';
//    }

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

//    #[Override]
//    public static function getNavigationSort(): ?int
//    {
//        return Utils::getResourceNavigationSort();
//    }

    #[Override]
    public static function getSlug(?Panel $panel = null): string
    {
        return Utils::getResourceSlug();
    }

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return Utils::isResourceNavigationBadgeEnabled()
    //            ? strval(self::getEloquentQuery()->count())
    //            : null;
    //    }

//    #[Override]
//    public static function isScopedToTenant(): bool
//    {
//        return Utils::isScopedToTenant();
//    }

//    #[Override]
//    public static function canGloballySearch(): bool
//    {
//        return Utils::isResourceGloballySearchable() && count(self::getGloballySearchableAttributes()) && self::canViewAny();
//    }

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

    /**
     * Get all resources from all Filament panels and map them to correct permission names
     *
     * @return \Illuminate\Support\Collection<string, array{resource:string,model:string,fqcn:string}>
     */
    private static function getAllResourcesFromAllPanels(): \Illuminate\Support\Collection
    {
        /** @var \Illuminate\Support\Collection<string, array{resource:string,model:string,fqcn:string}> $allResources */
        $allResources = collect();

        // Map of actual permission prefixes to model names based on existing permissions in database
        $permissionToModelMap = self::buildPermissionToModelMap();

        foreach ($permissionToModelMap as $permissionPrefix => $modelName) {
            $allResources->put($permissionPrefix, [
                'resource' => $permissionPrefix,
                'model' => $modelName,
                'fqcn' => "App\\Models\\{$modelName}",
            ]);
        }

        return $allResources;
    }

    /**
     * Build a map of permission prefixes to model names based on existing permissions
     *
     * @return array<string, string>
     */
    private static function buildPermissionToModelMap(): array
    {
        // Get all unique permission suffixes from database (excluding filament-panel and widget permissions)
        $permissions = \Spatie\Permission\Models\Permission::where('name', 'not like', 'filament-panel.%')
            ->where('name', 'not like', 'widget_%')
            ->get(['name'])
            ->pluck('name')
            ->map(function (mixed $permission): ?string {
                /** @var string $permission */
                // Extract the resource name from permission (e.g., 'view_any_currency' -> 'currency')
                if (preg_match('/^[a-z_]+_([a-z_]+)$/', $permission, $matches)) {
                    return $matches[1];
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values();

        // Map resource names to proper model names
        $modelMap = [
            'currency' => 'Currency',
            'role' => 'Role',
            'user' => 'User',
            'workspace' => 'Workspace',
        ];

        /** @var array<string, string> */
        return $permissions->mapWithKeys(function (mixed $resource) use ($modelMap): array {
            /** @var non-falsy-string $resource */
            $modelName = $modelMap[$resource] ?? str($resource)->studly()->toString();

            return [$resource => $modelName];
        })->toArray();
    }
}
