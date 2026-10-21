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
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Sistem E-Absensi Lazatto')
            ->brandLogo(fn () => view('filament.components.brand-logo'))
            ->brandLogoHeight('2.8rem')
            ->colors([
                'primary' => [
                    50 => '#fef2f2', 100 => '#fee2e2', 200 => '#fecaca',
                    300 => '#fca5a5', 400 => '#f87171', 500 => '#ef4444',
                    600 => '#dc2626', 700 => '#b91c1c', 800 => '#991b1b',
                    900 => '#7f1d1d', 950 => '#450a0a',
                ],
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'info' => Color::Orange,
                'success' => Color::Emerald,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->darkMode()
            ->navigationGroups([
                'Manajemen Karyawan',
                'Persetujuan & Izin',
                'Laporan & Presensi',
                'Pengaturan Kantor',
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString('
                    <style>
                        /* ==========================================
                           LAZATTO THEME - LIGHT MODE
                           ========================================== */
                        :root {
                            --lazatto-red: #b91c1c;
                            --lazatto-dark-red: #991b1b;
                            --lazatto-orange: #ea580c;
                            --lazatto-bright-orange: #f97316;
                            --lazatto-yellow: #facc15;
                        }

                        .fi-body, .fi-layout, .fi-main-ctn, .fi-main {
                            background-color: #fbf7f4 !important;
                        }

                        #fi-main-sidebar, .fi-sidebar, .fi-main-sidebar {
                            background: linear-gradient(180deg, #b91c1c 0%, #991b1b 60%, #7f1d1d 100%) !important;
                            border-right: 1px solid rgba(254, 215, 170, 0.25) !important;
                            box-shadow: 4px 0 25px rgba(127, 29, 29, 0.4) !important;
                        }

                        .fi-sidebar-header-ctn, .fi-sidebar-header, .fi-sidebar-nav, .fi-sidebar-nav-groups {
                            background: transparent !important;
                        }

                        .fi-sidebar-header {
                            border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
                            padding-top: 1rem !important;
                            padding-bottom: 1rem !important;
                        }
                        .fi-logo, a.fi-logo, .fi-sidebar-header .fi-logo {
                            height: 2.8rem !important;
                            max-height: 2.8rem !important;
                            display: inline-flex !important;
                            align-items: center !important;
                            background: transparent !important;
                            background-color: transparent !important;
                            border: none !important;
                            border-radius: 0 !important;
                            padding: 0 !important;
                            box-shadow: none !important;
                            overflow: visible !important;
                        }
                        .fi-logo img {
                            height: 2.6rem !important;
                            max-height: 2.8rem !important;
                            width: auto !important;
                            max-width: 140px !important;
                            object-fit: contain !important;
                            background: transparent !important;
                            border-radius: 0 !important;
                            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.4)) !important;
                        }

                        /* Group Headers - KUNING DIUBAH KE PUTIH */
                        .fi-sidebar-group-label,
                        .fi-sidebar-group-label span,
                        .fi-sidebar-group-btn span,
                        button.fi-sidebar-group-btn span {
                            color: #ffffff !important;
                            font-size: 0.72rem !important;
                            font-weight: 800 !important;
                            letter-spacing: 0.08em !important;
                            text-transform: uppercase !important;
                            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4) !important;
                        }
                        .fi-sidebar-group-btn svg {
                            color: #ffffff !important;
                        }

                        /* Sidebar Navigation Items */
                        .fi-sidebar-item-btn {
                            border-radius: 0.85rem !important;
                            margin: 0.25rem 0.5rem !important;
                            padding: 0.65rem 0.85rem !important;
                            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        .fi-sidebar-item-btn,
                        .fi-sidebar-item-btn span,
                        .fi-sidebar-item-label {
                            color: #ffffff !important;
                            font-weight: 600 !important;
                            font-size: 0.875rem !important;
                        }
                        .fi-sidebar-item .fi-sidebar-item-icon {
                            color: #fed7aa !important;
                            transition: color 0.2s ease, transform 0.2s ease !important;
                        }
                        .fi-sidebar-item-btn:hover {
                            background: linear-gradient(90deg, rgba(249, 115, 22, 0.45) 0%, rgba(220, 38, 38, 0.3) 100%) !important;
                            color: #ffffff !important;
                            transform: translateX(4px) !important;
                            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15) !important;
                        }
                        .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
                            color: #ffffff !important; /* Diubah ke putih */
                            transform: scale(1.1) !important;
                        }

                        /* Active Menu Item - KUNING DIUBAH KE PUTIH */
                        .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
                            background: linear-gradient(135deg, #f97316 0%, #dc2626 100%) !important;
                            box-shadow: 0 4px 16px rgba(234, 88, 12, 0.5) !important;
                            border: 1px solid #ffffff !important;
                        }
                        .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-label,
                        .fi-sidebar-item.fi-active > .fi-sidebar-item-btn span {
                            color: #ffffff !important;
                            font-weight: 800 !important;
                        }
                        .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-icon {
                            color: #ffffff !important;
                            filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.6)) !important;
                        }

                        .fi-sidebar-open-collapse-sidebar-btn,
                        .fi-sidebar-close-collapse-sidebar-btn,
                        .fi-layout-sidebar-toggle-btn,
                        .fi-topbar-open-sidebar-btn,
                        .fi-topbar-close-sidebar-btn {
                            color: #ffffff !important;
                        }

                        /* TOPBAR HEADER */
                        .fi-topbar-ctn {
                            background: transparent !important;
                            padding: 0 !important;
                            margin: 0 !important;
                            position: sticky !important;
                            top: 0 !important;
                            z-index: 30 !important;
                            width: 100% !important;
                        }
                        nav.fi-topbar,
                        .fi-topbar {
                            background: linear-gradient(135deg, #b91c1c 0%, #c5221f 50%, #ea580c 100%) !important;
                            border-radius: 0 !important;
                            border: none !important;
                            border-bottom: 2px solid rgba(254, 215, 170, 0.35) !important;
                            box-shadow: 0 4px 20px rgba(185, 28, 28, 0.25) !important;
                            padding: 0.55rem 1.25rem !important;
                            min-height: 4.25rem !important;
                            display: flex !important;
                            align-items: center !important;
                            color: #ffffff !important;
                        }
                        .fi-topbar-start {
                            display: flex !important;
                            align-items: center !important;
                            gap: 0.85rem !important;
                            margin-inline-end: auto !important;
                        }
                        .fi-topbar-start > a,
                        .fi-topbar-start > .fi-logo {
                            order: -1 !important;
                            margin-inline-start: 0 !important;
                            display: inline-flex !important;
                            align-items: center !important;
                            height: auto !important;
                        }
                        .fi-topbar-collapse-sidebar-btn-ctn {
                            order: 1 !important;
                            margin-inline-start: 0.35rem !important;
                        }

                        /* Toggle Sidebar Hover - KUNING DIUBAH KE PUTIH */
                        .fi-topbar-open-sidebar-btn,
                        .fi-topbar-close-sidebar-btn,
                        .fi-topbar-open-collapse-sidebar-btn,
                        .fi-topbar-close-collapse-sidebar-btn {
                            color: #ffffff !important;
                            background: rgba(255, 255, 255, 0.16) !important;
                            border: 1px solid rgba(254, 215, 170, 0.25) !important;
                            border-radius: 0.65rem !important;
                            padding: 0.35rem !important;
                            transition: all 0.2s ease !important;
                        }
                        .fi-topbar-open-sidebar-btn:hover,
                        .fi-topbar-close-sidebar-btn:hover,
                        .fi-topbar-open-collapse-sidebar-btn:hover,
                        .fi-topbar-close-collapse-sidebar-btn:hover {
                            background: rgba(255, 255, 255, 0.28) !important;
                            border-color: #ffffff !important;
                            transform: scale(1.05) !important;
                        }
                        .fi-topbar-open-sidebar-btn svg,
                        .fi-topbar-close-sidebar-btn svg,
                        .fi-topbar-open-collapse-sidebar-btn svg,
                        .fi-topbar-close-collapse-sidebar-btn svg {
                            color: #ffffff !important;
                        }

                        /* GLOBAL SEARCH - KUNING DIUBAH KE PUTIH */
                        .fi-topbar .fi-global-search-ctn {
                            max-width: 24rem !important;
                        }
                        .fi-topbar .fi-global-search-field,
                        .fi-topbar .fi-global-search-field .fi-input-wrp,
                        nav.fi-topbar .fi-input-wrp {
                            background-color: rgba(90, 10, 10, 0.45) !important;
                            background: rgba(90, 10, 10, 0.45) !important;
                            backdrop-filter: blur(8px) !important;
                            border: 1px solid rgba(254, 215, 170, 0.4) !important;
                            border-radius: 0.85rem !important;
                            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2) !important;
                            transition: all 0.2s ease !important;
                        }
                        .fi-topbar .fi-global-search-field .fi-input-wrp:focus-within,
                        nav.fi-topbar .fi-input-wrp:focus-within {
                            background-color: rgba(60, 5, 5, 0.65) !important;
                            background: rgba(60, 5, 5, 0.65) !important;
                            border-color: #ffffff !important;
                            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.35), inset 0 2px 4px rgba(0, 0, 0, 0.2) !important;
                        }
                        .fi-topbar .fi-global-search-field input,
                        nav.fi-topbar input {
                            background: transparent !important;
                            background-color: transparent !important;
                            color: #ffffff !important;
                            font-weight: 600 !important;
                            font-size: 0.875rem !important;
                        }
                        .fi-topbar .fi-global-search-field input::placeholder,
                        nav.fi-topbar input::placeholder {
                            color: rgba(255, 240, 220, 0.85) !important;
                            font-weight: 500 !important;
                        }
                        .fi-topbar .fi-global-search-field .fi-input-wrp-icon,
                        .fi-topbar .fi-global-search-field svg,
                        nav.fi-topbar .fi-global-search-field svg {
                            color: #ffffff !important;
                            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.35)) !important;
                        }

                        /* User Menu Trigger & Avatar - KUNING DIUBAH KE PUTIH */
                        nav.fi-topbar .fi-user-menu button,
                        .fi-topbar .fi-user-menu button {
                            background: rgba(255, 255, 255, 0.18) !important;
                            border-radius: 9999px !important;
                            padding: 0.25rem 0.5rem !important;
                            border: 1px solid rgba(254, 215, 170, 0.35) !important;
                            transition: all 0.2s ease !important;
                        }
                        nav.fi-topbar .fi-user-menu button:hover,
                        .fi-topbar .fi-user-menu button:hover {
                            background: rgba(255, 255, 255, 0.3) !important;
                            border-color: #ffffff !important;
                        }
                        nav.fi-topbar .fi-avatar,
                        .fi-topbar .fi-avatar {
                            background: #ffffff !important;
                            color: #b91c1c !important;
                            font-weight: 800 !important;
                            border: 2px solid #ffffff !important;
                            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25) !important;
                        }

                        /* THEME SWITCHER */
                        .fi-theme-switcher {
                            background: #fff7ed !important;
                            padding: 0.25rem !important;
                            border-radius: 0.75rem !important;
                            border: 1px solid #fed7aa !important;
                            gap: 0.25rem !important;
                        }
                        .fi-theme-switcher-btn {
                            border-radius: 0.5rem !important;
                            color: #9a3412 !important;
                            transition: all 0.15s ease !important;
                        }
                        .fi-theme-switcher-btn svg {
                            color: #ea580c !important;
                            width: 1.15rem !important;
                            height: 1.15rem !important;
                        }
                        .fi-theme-switcher-btn:hover {
                            background: #ffedd5 !important;
                        }
                        .fi-theme-switcher-btn.fi-active {
                            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%) !important;
                            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35) !important;
                        }
                        .fi-theme-switcher-btn.fi-active svg {
                            color: #ffffff !important;
                        }

                        /* STATS OVERVIEW CARDS */
                        .fi-wi-stats-overview {
                            background: transparent !important;
                            border: none !important;
                            box-shadow: none !important;
                        }
                        .fi-wi-stats-overview-stat {
                            background: linear-gradient(180deg, #ffffff 0%, #fff7ed 100%) !important;
                            border-radius: 1.25rem !important;
                            border: 1px solid #fed7aa !important;
                            border-top: 4px solid #ea580c !important;
                            box-shadow: 0 4px 18px rgba(234, 88, 12, 0.1) !important;
                            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
                        }
                        .fi-wi-stats-overview-stat:hover {
                            transform: translateY(-3px) !important;
                            box-shadow: 0 8px 24px rgba(234, 88, 12, 0.2) !important;
                        }
                        .fi-wi-stats-overview-stat-label {
                            color: #9a3412 !important;
                            font-weight: 700 !important;
                        }
                        .fi-wi-stats-overview-stat-value {
                            color: #7c2d12 !important;
                            font-weight: 800 !important;
                        }
                        .fi-wi-stats-overview-stat-description {
                            font-weight: 600 !important;
                        }

                        /* SECTION & WIDGET CARD STYLING */
                        .fi-section {
                            background: #ffffff !important;
                            border-radius: 1.25rem !important;
                            border: 1px solid #fed7aa !important;
                            box-shadow: 0 4px 20px -2px rgba(234, 88, 12, 0.09) !important;
                            overflow: hidden !important;
                        }
                        .fi-section-header {
                            background: linear-gradient(90deg, #fef2f2 0%, #fff7ed 100%) !important;
                            border-bottom: 2px solid #fed7aa !important;
                            padding: 1rem 1.25rem !important;
                            display: flex !important;
                            align-items: flex-start !important;
                            justify-content: space-between !important;
                            gap: 1rem !important;
                        }
                        .fi-wi-chart .fi-section-header {
                            display: flex !important;
                            flex-wrap: wrap !important;
                            align-items: center !important;
                            justify-content: space-between !important;
                            gap: 0.75rem 1rem !important;
                        }
                        .fi-wi-chart .fi-section-header-text-ctn {
                            flex: 1 1 0% !important;
                            width: 100% !important;
                            display: contents !important;
                        }
                        .fi-wi-chart .fi-section-header-heading {
                            order: 1 !important;
                            flex: 1 1 auto !important;
                        }
                        .fi-wi-chart .fi-section-header-after-ctn {
                            order: 2 !important;
                            flex: 0 0 auto !important;
                            margin-top: 0 !important;
                            align-self: center !important;
                        }
                        .fi-wi-chart .fi-section-header-description {
                            order: 3 !important;
                            width: 100% !important;
                            flex: 1 0 100% !important;
                        }
                        .fi-wi-chart-legend-box {
                            width: 100% !important;
                            box-sizing: border-box !important;
                        }
                        .fi-section-header-text-ctn {
                            flex: 1 1 auto !important;
                        }
                        .fi-section-header-after-ctn {
                            flex: 0 0 auto !important;
                            align-self: flex-start !important;
                            margin-top: 0 !important;
                        }
                        .fi-wi-chart-filter {
                            min-width: 140px !important;
                            max-width: 180px !important;
                        }
                        .fi-wi-chart-filter select {
                            padding-top: 0.25rem !important;
                            padding-bottom: 0.25rem !important;
                            font-size: 0.8125rem !important;
                            font-weight: 600 !important;
                            border-radius: 0.65rem !important;
                        }
                        .fi-header-heading,
                        .fi-section-header-heading {
                            color: #991b1b !important;
                            font-weight: 800 !important;
                            letter-spacing: -0.025em !important;
                        }

                        /* =======================================================
                           INI GRID NYA YANG SEMPAT HILANG, SAYA KEMBALIKAN UTUH!
                           ======================================================= */
                        @media (min-width: 768px) {
                            .fi-wi-chart {
                                grid-column: span 1 / span 1 !important;
                                display: flex !important;
                                flex-direction: column !important;
                                height: 100% !important;
                            }
                            .fi-wi-chart .fi-section {
                                flex: 1 1 auto !important;
                                display: flex !important;
                                flex-direction: column !important;
                                height: 100% !important;
                            }
                            .fi-wi-chart .fi-section-header {
                                min-height: 145px !important;
                            }
                            .fi-wi-chart .fi-section-content-ctn {
                                flex: 1 1 auto !important;
                                display: flex !important;
                                flex-direction: column !important;
                                justify-content: center !important;
                            }
                            .fi-wi-chart .fi-wi-chart-canvas-ctn {
                                max-height: 320px !important;
                            }
                        }

                        .fi-wi-chart:first-of-type .fi-section {
                            border-top: 4px solid #dc2626 !important;
                        }
                        .fi-wi-chart:nth-of-type(2) .fi-section,
                        .fi-wi-chart:last-child .fi-section {
                            background: linear-gradient(180deg, #ffffff 0%, #fffaf5 100%) !important;
                            border: 1px solid #fdba74 !important;
                            border-top: 4px solid #ea580c !important;
                            box-shadow: 0 6px 24px rgba(234, 88, 12, 0.14) !important;
                        }
                        .fi-wi-chart:nth-of-type(2) .fi-section-header,
                        .fi-wi-chart:last-child .fi-section-header {
                            background: linear-gradient(90deg, #fff7ed 0%, #ffedd5 100%) !important;
                            border-bottom: 2px solid #fed7aa !important;
                        }

                        /* Slide-over Kanan & Modal */
                        .fi-modal-window {
                            border: 1px solid #fed7aa !important;
                            border-left: 5px solid #ea580c !important;
                            border-radius: 1.25rem !important;
                            overflow: hidden !important;
                            box-shadow: -8px 0 35px rgba(234, 88, 12, 0.22) !important;
                        }
                        .fi-modal-header {
                            background: linear-gradient(135deg, #b91c1c 0%, #ea580c 100%) !important;
                            color: #ffffff !important;
                            padding: 1.25rem !important;
                        }
                        .fi-modal-heading,
                        .fi-modal-header button,
                        .fi-modal-header .fi-icon-btn,
                        .fi-modal-header svg {
                            color: #ffffff !important;
                            font-weight: 700 !important;
                        }
                        .fi-modal-content {
                            background: #fffaf5 !important;
                        }
                        .fi-modal-footer {
                            background: #fff7ed !important;
                            border-top: 1px solid #fed7aa !important;
                        }

                        /* Tables */
                        .fi-ta-ctn {
                            background: #ffffff !important;
                            border-radius: 1.25rem !important;
                            border: 1px solid #fed7aa !important;
                            border-top: 4px solid #dc2626 !important;
                            box-shadow: 0 4px 18px rgba(220, 38, 38, 0.08) !important;
                            overflow: hidden !important;
                        }
                        .fi-ta-header-cell {
                            background: linear-gradient(90deg, #fef2f2 0%, #fff7ed 100%) !important;
                            border-bottom: 2px solid #fecaca !important;
                            color: #991b1b !important;
                            font-weight: 800 !important;
                            font-size: 0.75rem !important;
                            text-transform: uppercase !important;
                        }
                        .fi-ta-row:hover,
                        .fi-ta-record:hover {
                            background-color: #fff7ed !important;
                        }
                        .fi-pagination-item-active button {
                            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%) !important;
                            color: #ffffff !important;
                            font-weight: 700 !important;
                            border: none !important;
                        }

                        /* Primary Buttons */
                        .fi-btn-color-primary:not(.fi-btn-outlined) {
                            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%) !important;
                            color: #ffffff !important;
                            border-radius: 0.75rem !important;
                            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35) !important;
                            border: none !important;
                            font-weight: 700 !important;
                            transition: all 0.2s ease !important;
                        }
                        .fi-btn-color-primary:not(.fi-btn-outlined):hover {
                            background: linear-gradient(135deg, #f97316 0%, #b91c1c 100%) !important;
                            transform: translateY(-1px) !important;
                            box-shadow: 0 6px 18px rgba(234, 88, 12, 0.45) !important;
                        }

                        /* Form Inputs Focus */
                        .fi-input-wrp:focus-within {
                            border-color: #ea580c !important;
                            box-shadow: 0 0 0 2px rgba(234, 88, 12, 0.25) !important;
                        }

                        /* Badges */
                        .fi-badge-color-primary { background: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fca5a5 !important; font-weight: 700 !important; }
                        .fi-badge-color-warning { background: #fef3c7 !important; color: #92400e !important; border: 1px solid #fde68a !important; font-weight: 700 !important; }
                        .fi-badge-color-info { background: #ffedd5 !important; color: #9a3412 !important; border: 1px solid #fed7aa !important; font-weight: 700 !important; }

                        /* Dropdowns */
                        .fi-dropdown-panel {
                            border-radius: 1rem !important;
                            border: 1px solid #fed7aa !important;
                            box-shadow: 0 10px 30px rgba(234, 88, 12, 0.15) !important;
                            background: #ffffff !important;
                            overflow: hidden !important;
                        }
                        .fi-dropdown-list-item:hover {
                            background: #fff7ed !important;
                            color: #b91c1c !important;
                        }

                        /* Scrollbar Lazatto */
                        ::-webkit-scrollbar { width: 6px; height: 6px; }
                        ::-webkit-scrollbar-track { background: rgba(234, 88, 12, 0.05); }
                        ::-webkit-scrollbar-thumb { background: #fdba74; border-radius: 9999px; }
                        ::-webkit-scrollbar-thumb:hover { background: #ea580c; }

                        /* DARK MODE OVERRIDES */
                        html.dark .fi-body,
                        html.dark .fi-layout,
                        html.dark .fi-main-ctn,
                        html.dark .fi-main {
                            background-color: #1a0a0a !important;
                        }
                        html.dark #fi-main-sidebar,
                        html.dark .fi-sidebar,
                        html.dark .fi-main-sidebar {
                            background: linear-gradient(180deg, #7f1d1d 0%, #450a0a 60%, #1c0505 100%) !important;
                            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.6) !important;
                        }
                        html.dark nav.fi-topbar,
                        html.dark .fi-topbar {
                            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #c2410c 100%) !important;
                            border-bottom: 2px solid rgba(234, 88, 12, 0.45) !important;
                            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.5) !important;
                        }
                        html.dark .fi-topbar .fi-global-search-field .fi-input-wrp,
                        html.dark nav.fi-topbar .fi-input-wrp {
                            background-color: rgba(20, 5, 5, 0.6) !important;
                            background: rgba(20, 5, 5, 0.6) !important;
                            border: 1px solid rgba(234, 88, 12, 0.35) !important;
                        }
                        html.dark .fi-theme-switcher { background: #200707 !important; border: 1px solid rgba(234, 88, 12, 0.3) !important; }
                        html.dark .fi-theme-switcher-btn { color: #fed7aa !important; }
                        html.dark .fi-theme-switcher-btn svg { color: #fdba74 !important; }
                        html.dark .fi-theme-switcher-btn:hover { background: #340c0c !important; }
                        
                        /* ACTIVE THEME SWITCHER - KUNING DIUBAH KE PUTIH */
                        html.dark .fi-theme-switcher-btn.fi-active { background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%) !important; border: 1px solid #ffffff !important; }
                        html.dark .fi-theme-switcher-btn.fi-active svg { color: #ffffff !important; }

                        html.dark .fi-wi-stats-overview-stat { background: linear-gradient(180deg, #1e0505 0%, #2d0a0a 100%) !important; border: 1px solid rgba(234, 88, 12, 0.4) !important; border-top: 4px solid #ea580c !important; }
                        html.dark .fi-wi-stats-overview-stat-label { color: #fdba74 !important; }
                        html.dark .fi-wi-stats-overview-stat-value { color: #fed7aa !important; }

                        html.dark .fi-section { background: #1e0505 !important; border: 1px solid rgba(234, 88, 12, 0.3) !important; }
                        html.dark .fi-section-header { background: linear-gradient(90deg, #2d0a0a 0%, #3b0f0f 100%) !important; border-bottom: 2px solid rgba(234, 88, 12, 0.4) !important; }
                        html.dark .fi-header-heading, html.dark .fi-section-header-heading { color: #fed7aa !important; }
                        html.dark .fi-wi-chart:first-of-type .fi-section { border-top: 4px solid #dc2626 !important; }
                        html.dark .fi-wi-chart:nth-of-type(2) .fi-section, html.dark .fi-wi-chart:last-child .fi-section { background: linear-gradient(180deg, #1e0a00 0%, #150500 100%) !important; border-top: 4px solid #ea580c !important; }
                        html.dark .fi-wi-chart:nth-of-type(2) .fi-section-header, html.dark .fi-wi-chart:last-child .fi-section-header { background: linear-gradient(90deg, #2d1505 0%, #3b1a0a 100%) !important; }

                        html.dark .fi-ta-ctn { background: #1e0505 !important; border: 1px solid rgba(234, 88, 12, 0.3) !important; border-top: 4px solid #dc2626 !important; }
                        html.dark .fi-ta-header-cell { background: linear-gradient(90deg, #2d0a0a 0%, #2d1505 100%) !important; color: #fdba74 !important; border-bottom: 2px solid rgba(234, 88, 12, 0.4) !important; }
                        html.dark .fi-ta-row:hover, html.dark .fi-ta-record:hover { background-color: #2d0a0a !important; }
                        html.dark .fi-ta-cell { color: #fde8c8 !important; }

                        html.dark .fi-modal-window { border: 1px solid rgba(234, 88, 12, 0.4) !important; border-left: 5px solid #ea580c !important; }
                        html.dark .fi-modal-content { background: #1e0505 !important; }
                        html.dark .fi-modal-footer { background: #2d0a0a !important; border-top: 1px solid rgba(234, 88, 12, 0.3) !important; }

                        html.dark .fi-dropdown-panel { background: #1e0505 !important; border: 1px solid rgba(234, 88, 12, 0.3) !important; }
                        html.dark .fi-dropdown-list-item:hover { background: #2d0a0a !important; color: #fdba74 !important; }
                        html.dark .fi-dropdown-list-item { color: #fde8c8 !important; }

                        html.dark .fi-badge-color-primary { background: #450a0a !important; color: #fca5a5 !important; border-color: #7f1d1d !important; }

                        html.dark .fi-input, html.dark input.fi-input, html.dark .fi-fo-field-wrp input, html.dark .fi-fo-field-wrp textarea, html.dark .fi-fo-field-wrp select { background-color: #2d0a0a !important; color: #fde8c8 !important; border-color: rgba(234, 88, 12, 0.4) !important; }
                        html.dark .fi-breadcrumbs { color: #fdba74 !important; }
                        html.dark ::-webkit-scrollbar-track { background: rgba(127, 29, 29, 0.2); }
                        html.dark ::-webkit-scrollbar-thumb { background: #7f1d1d; }
                        html.dark ::-webkit-scrollbar-thumb:hover { background: #b91c1c; }

                        /* ==========================================
                           RESPONSIVE - Mobile & Tablet
                           ========================================== */
                        @media (max-width: 768px) {
                            nav.fi-topbar,
                            .fi-topbar {
                                padding: 0.5rem 0.75rem !important;
                            }
                        }

                        @media (max-width: 640px) {
                            .fi-wi-stats-overview {
                                display: grid !important;
                                grid-template-columns: 1fr 1fr !important;
                                gap: 0.75rem !important;
                            }
                            .fi-wi-stats-overview-stat {
                                border-radius: 1rem !important;
                            }
                            .fi-section {
                                border-radius: 1rem !important;
                            }
                            .fi-ta-ctn {
                                border-radius: 1rem !important;
                            }
                        }
                        @media (max-width: 400px) {
                            .fi-wi-stats-overview {
                                grid-template-columns: 1fr !important;
                            }
                        }

                        /* ==========================================
                           LEGEND HIGHLIGHT - Pulsing & Glow Animation
                           ========================================== */
                        @keyframes legendPulse {
                            0% { transform: scale(1); opacity: 0.75; }
                            70% { transform: scale(2.5); opacity: 0; }
                            100% { transform: scale(2.5); opacity: 0; }
                        }

                        @keyframes legendBorderGlow {
                            0%, 100% { border-color: #f59e0b; box-shadow: 0 0 18px rgba(245, 158, 11, 0.28), 0 4px 14px rgba(234, 88, 12, 0.12); }
                            50% { border-color: #ea580c; box-shadow: 0 0 24px rgba(234, 88, 12, 0.35), 0 4px 14px rgba(234, 88, 12, 0.18); }
                        }

                        .fi-legend-highlight {
                            animation: legendBorderGlow 3s ease-in-out infinite !important;
                        }

                        html.dark .fi-legend-highlight {
                            background: linear-gradient(135deg, #1c1305 0%, #200a0a 60%, #1e0a00 100%) !important;
                            border-color: #b45309 !important;
                            color: #fde8c8 !important;
                        }
                        html.dark .fi-legend-highlight div {
                            color: #fde8c8 !important;
                        }
                    </style>
                ')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): HtmlString => new HtmlString('
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            ' . (session('swal_success') ? '
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: ' . json_encode(session('swal_success')) . ',
                                confirmButtonColor: "#f97316",
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                background: "#f0fdf4",
                                color: "#1f2937",
                                customClass: { popup: "swal-rounded" }
                            });
                            ' : '') . '

                            ' . (session('swal_info') ? '
                            Swal.fire({
                                icon: "info",
                                title: "Informasi",
                                text: ' . json_encode(session('swal_info')) . ',
                                confirmButtonColor: "#f97316",
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                background: "#eff6ff",
                                color: "#1f2937",
                                customClass: { popup: "swal-rounded" }
                            });
                            ' : '') . '

                            ' . (session('swal_error') ? '
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: ' . json_encode(session('swal_error')) . ',
                                confirmButtonColor: "#f97316",
                                confirmButtonText: "OK",
                                background: "#fef7ed",
                                color: "#1f2937",
                                customClass: { popup: "swal-rounded" }
                            });
                            ' : '') . '

                            if (typeof Livewire !== "undefined") {
                                Livewire.hook("commit", ({ component, commit, respond, succeed, fail }) => {
                                    succeed(({ snapshot, effects }) => {
                                        if (effects.dispatches) {
                                            effects.dispatches.forEach(function(dispatch) {
                                                if (dispatch.name === "notification-sent" || dispatch.name === "notificationSent") {
                                                    // Handled by observer
                                                }
                                            });
                                        }
                                    });
                                });

                                document.addEventListener("notification-sent", function(event) {
                                    let n = event.detail || {};
                                    let title = n.title || "Notifikasi";
                                    let body = n.body || "";
                                    let status = n.status || n.color || "success";
                                    
                                    let iconMap = { success: "success", warning: "warning", danger: "error", info: "info" };
                                    let bgMap = { success: "#f0fdf4", warning: "#fffbeb", danger: "#fef7ed", info: "#eff6ff" };
                                    
                                    Swal.fire({
                                        icon: iconMap[status] || "success",
                                        title: title,
                                        text: body,
                                        confirmButtonColor: "#f97316",
                                        timer: 3000,
                                        timerProgressBar: true,
                                        showConfirmButton: false,
                                        background: bgMap[status] || "#f0fdf4",
                                        color: "#1f2937",
                                        customClass: { popup: "swal-rounded" }
                                    });
                                });

                                const observer = new MutationObserver(function(mutations) {
                                    mutations.forEach(function(mutation) {
                                        mutation.addedNodes.forEach(function(node) {
                                            if (node.nodeType === 1) {
                                                let notif = node.closest ? node.closest("[wire\\\\:notification]") || node.querySelector("[wire\\\\:notification]") : null;
                                                if (!notif && node.classList && (node.classList.contains("fi-no-notification") || node.classList.contains("fi-notification"))) {
                                                    notif = node;
                                                }
                                                if (notif) {
                                                    let titleEl = notif.querySelector(".fi-no-notification-title, .fi-notification-title, [class*=\\"title\\"]");
                                                    let bodyEl = notif.querySelector(".fi-no-notification-body, .fi-notification-body, [class*=\\"body\\"]");
                                                    let title = titleEl ? titleEl.textContent.trim() : "Berhasil";
                                                    let body = bodyEl ? bodyEl.textContent.trim() : "";
                                                    
                                                    let icon = "success";
                                                    let bg = "#f0fdf4";
                                                    if (notif.innerHTML.includes("danger") || notif.innerHTML.includes("error")) { icon = "error"; bg = "#fef7ed"; }
                                                    else if (notif.innerHTML.includes("warning")) { icon = "warning"; bg = "#fffbeb"; }
                                                    else if (notif.innerHTML.includes("info")) { icon = "info"; bg = "#eff6ff"; }

                                                    Swal.fire({
                                                        icon: icon,
                                                        title: title,
                                                        text: body,
                                                        confirmButtonColor: "#f97316",
                                                        timer: 3000,
                                                        timerProgressBar: true,
                                                        showConfirmButton: false,
                                                        background: bg,
                                                        color: "#1f2937",
                                                        customClass: { popup: "swal-rounded" }
                                                    });
                                                    
                                                    notif.style.display = "none";
                                                }
                                            }
                                        });
                                    });
                                });
                                observer.observe(document.body, { childList: true, subtree: true });
                            }
                        });
                    </script>
                    <style>.swal-rounded { border-radius: 1rem !important; font-family: Plus Jakarta Sans, sans-serif !important; }</style>
                ')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
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