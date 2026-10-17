<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class KaryawanPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('karyawan')
            ->path('karyawan')
            ->brandName('Portal Karyawan Lazatto')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('7xl')
            ->navigationGroups([
                'Menu Utama',
                'Pengajuan',
                'Laporan',
                'Pengaturan',
            ])
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make()
                    ->label('Profil Saya')
                    ->url(fn (): string => \App\Filament\Karyawan\Pages\ProfilKaryawan::getUrl())
                    ->icon('heroicon-o-user-circle'),
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                function (): \Illuminate\Support\HtmlString {
                    $manifestPath = public_path('build/manifest.json');
                    if (file_exists($manifestPath)) {
                        $manifest = json_decode(file_get_contents($manifestPath), true);
                        $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
                        if ($cssFile) {
                            return new \Illuminate\Support\HtmlString('<link rel="stylesheet" href="/build/' . $cssFile . '">');
                        }
                    }
                    return new \Illuminate\Support\HtmlString('');
                }
            )
            ->discoverResources(in: app_path('Filament/Karyawan/Resources'), for: 'App\Filament\Karyawan\Resources')
            ->discoverPages(in: app_path('Filament/Karyawan/Pages'), for: 'App\Filament\Karyawan\Pages')
            ->pages([
                \App\Filament\Karyawan\Pages\DashboardKaryawan::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Karyawan/Widgets'), for: 'App\Filament\Karyawan\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
