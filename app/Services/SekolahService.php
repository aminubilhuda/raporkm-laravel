<?php

namespace App\Services;

use App\Models\Sekolah;

class SekolahService
{
    private ?Sekolah $cachedSekolah = null;

    /**
     * Get the first (usually only) sekolah record.
     * Caches the result for the current request to avoid repeated queries.
     */
    public function get(): ?Sekolah
    {
        if ($this->cachedSekolah === null) {
            $this->cachedSekolah = Sekolah::first();
        }

        return $this->cachedSekolah;
    }

    /**
     * Get active tahun_pelajaran_id from sekolah.
     */
    public function getTahunAktif(): ?int
    {
        return $this->get()?->tahun_aktif;
    }

    /**
     * Get active semester_id from sekolah.
     */
    public function getSemesterAktif(): ?int
    {
        return $this->get()?->semester_aktif;
    }

    /**
     * Get active period IDs, falling back to session or sekolah config.
     *
     * @return array{tahun_pelajaran_id: ?int, semester_id: ?int}
     */
    public function getActivePeriodFromSession(): array
    {
        return [
            'tahun_pelajaran_id' => session('selected_tahun', $this->getTahunAktif()),
            'semester_id' => session('selected_semester', $this->getSemesterAktif()),
        ];
    }
}
