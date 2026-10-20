<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\User;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile.foto_wajah_acuan')
                    ->label('Foto')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn (User $record) => $record->avatar_url),
                TextColumn::make('profile.nik')
                    ->label('NIK')
                    ->placeholder('-')
                    ->weight('bold')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Karyawan')
                    ->description(fn (User $record): ?string => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable(),
                TextColumn::make('profile.telphone')
                    ->label('No. HP / WA')
                    ->placeholder('-')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('cabang.nama_cabang')
                    ->label('Cabang')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'danger',
                        'karyawan' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        default => $state,
                    }),
                TextColumn::make('status_biometrik')
                    ->label('Biometrik Master')
                    ->badge()
                    ->state(function (User $record): string {
                        if ($record->profile?->is_biometric_locked && $record->profile?->foto_wajah_acuan) {
                            return 'Sudah Verifikasi';
                        }
                        return 'Belum Rekam';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Sudah Verifikasi' => 'success',
                        default => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Sudah Verifikasi' => 'heroicon-m-check-badge',
                        default => 'heroicon-m-x-circle',
                    })
                    ->description(function (User $record): ?string {
                        if ($record->profile?->wajah_terverifikasi_at) {
                            return \Carbon\Carbon::parse($record->profile->wajah_terverifikasi_at)->format('d M Y H:i');
                        }
                        return null;
                    }),

                // =========================================================================================================
                // [FITUR PRIVASI & ENKRIPSI KTP - BLIND VERIFICATION SUPERADMIN PADA TABEL]
                // CATATAN DEVELOPER: Superadmin hanya melihat tanda centang simpel apakah KTP sudah diupload atau belum,
                // tanpa dapat melihat/mengakses/mengunduh fisik foto KTP demi perlindungan privasi data pribadi.
                // JIKA KLIEN INGIN FITUR INI DIUBAH/DIHAPUS, BAGIAN INI BISA DI-COMMENT ATAU DIHAPUS.
                // =========================================================================================================
                TextColumn::make('status_ktp')
                    ->label('KTP')
                    ->badge()
                    ->state(fn (User $record): string => filled($record->profile?->foto_ktp) ? 'Sudah Upload' : 'Belum')
                    ->color(fn (string $state): string => match ($state) {
                        'Sudah Upload' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Sudah Upload' => 'heroicon-m-check-badge',
                        default => 'heroicon-m-minus-circle',
                    })
                    ->tooltip(fn (User $record): string => filled($record->profile?->foto_ktp) 
                        ? 'Berkas KTP telah diunggah & terenkripsi aman' 
                        : 'Karyawan belum mengunggah berkas KTP'),
                // =========================================================================================================
                // [AKHIR FITUR PRIVASI & ENKRIPSI KTP]
                // =========================================================================================================
                TextColumn::make('profile.alamat')
                    ->label('Alamat')
                    ->placeholder('-')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('cabang_id')
                    ->label('Filter Cabang')
                    ->relationship('cabang', 'nama_cabang'),
                SelectFilter::make('status_biometrik')
                    ->label('Status Biometrik')
                    ->options([
                        'sudah' => 'Sudah Verifikasi Wajah',
                        'belum' => 'Belum Rekam Wajah',
                    ])
                    ->query(function ($query, array $data) {
                        if (($data['value'] ?? null) === 'sudah') {
                            $query->whereHas('profile', fn ($q) => $q->where('is_biometric_locked', true)->whereNotNull('foto_wajah_acuan'));
                        } elseif (($data['value'] ?? null) === 'belum') {
                            $query->whereDoesntHave('profile', fn ($q) => $q->where('is_biometric_locked', true)->whereNotNull('foto_wajah_acuan'));
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                \Filament\Actions\Action::make('resetBiometrik')
                    ->label('Reset Wajah')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->tooltip('Reset Biometrik Wajah')
                    ->visible(fn (User $record) => (bool) $record->profile?->foto_wajah_acuan)
                    ->requiresConfirmation()
                    ->modalHeading('Reset Data Biometrik Karyawan')
                    ->modalDescription(fn (User $record) => "Data biometrik wajah karyawan {$record->name} akan dihapus agar karyawan dapat mendaftarkan ulang wajahnya.")
                    ->modalSubmitActionLabel('Reset Data')
                    ->action(function (User $record) {
                        if ($record->profile) {
                            if ($record->profile->foto_wajah_acuan) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($record->profile->foto_wajah_acuan);
                            }
                            $record->profile->update([
                                'foto_wajah_acuan' => null,
                                'is_biometric_locked' => false,
                                'wajah_terverifikasi_at' => null,
                                'face_descriptor' => null,
                            ]);
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('Biometrik Berhasil Direset')
                            ->body("Data wajah karyawan {$record->name} telah dihapus. Karyawan dapat mendaftarkan ulang.")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
