<?php

namespace App\Support\Tenancy;

use App\Models\Mitra;
use RuntimeException;

final class MitraContext
{
    private ?Mitra $mitra = null;

    public function set(Mitra $mitra): void
    {
        $this->mitra = $mitra;
    }

    public function clear(): void
    {
        $this->mitra = null;
    }

    public function has(): bool
    {
        return $this->mitra !== null;
    }

    public function mitra(): Mitra
    {
        return $this->mitra ?? throw new RuntimeException('MitraContext belum diisi.');
    }

    public function id(): int
    {
        return $this->mitra()->id;
    }
}