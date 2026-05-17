<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{

    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */

    // Digunakan untuk menjaga keamanan data dengan memastikan header Host hanya berasal dari sumber yang tepercaya misal https://tebaslahan.id, mencegah serangan seperti Host Header Injection. jika domain tidak cocok, request akan ditolak.
    public function hosts(): array
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
