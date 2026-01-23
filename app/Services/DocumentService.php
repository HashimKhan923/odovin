<?php
namespace App\Services;
class DocumentService
{
    public function expiring($vehicles, int $days = 30)
    {
        return $vehicles->flatMap(
            fn ($vehicle) => $vehicle->getExpiringDocuments($days)
        );
    }
}