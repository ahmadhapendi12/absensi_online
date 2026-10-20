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
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                function (): \Illuminate\Support\HtmlString {
                    $js = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                    $js .= '<script>document.addEventListener("DOMContentLoaded", function() {';

                    if (session('swal_success')) {
                        $js .= 'Swal.fire({icon:"success",title:"Berhasil!",text:' . json_encode(session('swal_success')) . ',confirmButtonColor:"#f97316",timer:3000,timerProgressBar:true,showConfirmButton:false,background:"#f0fdf4",color:"#1f2937",customClass:{popup:"swal-rounded"}});';
                    }
                    if (session('swal_info')) {
                        $js .= 'Swal.fire({icon:"info",title:"Informasi",text:' . json_encode(session('swal_info')) . ',confirmButtonColor:"#f97316",timer:3000,timerProgressBar:true,showConfirmButton:false,background:"#eff6ff",color:"#1f2937",customClass:{popup:"swal-rounded"}});';
                    }
                    if (session('swal_error')) {
                        $js .= 'Swal.fire({icon:"error",title:"Gagal!",text:' . json_encode(session('swal_error')) . ',confirmButtonColor:"#f97316",confirmButtonText:"OK",background:"#fef7ed",color:"#1f2937",customClass:{popup:"swal-rounded"}});';
                    }

                    $js .= '
                    if (typeof Livewire !== "undefined") {
                        Livewire.hook("commit", ({ component, commit, respond, succeed, fail }) => {
                            succeed(({ snapshot, effects }) => {
                                if (effects.dispatches) {
                                    effects.dispatches.forEach(function(dispatch) {
                                        if (dispatch.name === "notification-sent" || dispatch.name === "notificationSent") {}
                                    });
                                }
                            });
                        });

                        document.addEventListener("notification-sent", function(event) {
                            let n = event.detail || {};
                            let title = n.title || "Notifikasi";
                            let body = n.body || "";
                            let status = n.status || n.color || "success";
                            let iconMap = {success:"success",warning:"warning",danger:"error",info:"info"};
                            let bgMap = {success:"#f0fdf4",warning:"#fffbeb",danger:"#fef7ed",info:"#eff6ff"};
                            Swal.fire({icon:iconMap[status]||"success",title:title,text:body,confirmButtonColor:"#f97316",timer:3000,timerProgressBar:true,showConfirmButton:false,background:bgMap[status]||"#f0fdf4",color:"#1f2937",customClass:{popup:"swal-rounded"}});
                        });

                        const observer = new MutationObserver(function(mutations) {
                            mutations.forEach(function(mutation) {
                                mutation.addedNodes.forEach(function(node) {
                                    if (node.nodeType === 1) {
                                        let notif = node.closest ? node.closest("[wire\\\\:notification]") || node.querySelector("[wire\\\\:notification]") : null;
                                        if (!notif && node.classList && (node.classList.contains("fi-no-notification") || node.classList.contains("fi-notification"))) { notif = node; }
                                        if (notif) {
                                            let titleEl = notif.querySelector(".fi-no-notification-title, .fi-notification-title, [class*=\\"title\\"]");
                                            let bodyEl = notif.querySelector(".fi-no-notification-body, .fi-notification-body, [class*=\\"body\\"]");
                                            let title = titleEl ? titleEl.textContent.trim() : "Berhasil";
                                            let body = bodyEl ? bodyEl.textContent.trim() : "";
                                            let icon = "success"; let bg = "#f0fdf4";
                                            if (notif.innerHTML.includes("danger") || notif.innerHTML.includes("error")) { icon="error"; bg="#fef7ed"; }
                                            else if (notif.innerHTML.includes("warning")) { icon="warning"; bg="#fffbeb"; }
                                            else if (notif.innerHTML.includes("info")) { icon="info"; bg="#eff6ff"; }
                                            Swal.fire({icon:icon,title:title,text:body,confirmButtonColor:"#f97316",timer:3000,timerProgressBar:true,showConfirmButton:false,background:bg,color:"#1f2937",customClass:{popup:"swal-rounded"}});
                                            notif.style.display = "none";
                                        }
                                    }
                                });
                            });
                        });
                        observer.observe(document.body, { childList: true, subtree: true });
                    }
                    ';

                    $js .= '});</script>';
                    $js .= '<style>.swal-rounded{border-radius:1rem!important;font-family:Plus Jakarta Sans,sans-serif!important;}</style>';

                    return new \Illuminate\Support\HtmlString($js);
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
