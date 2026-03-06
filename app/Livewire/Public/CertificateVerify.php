<?php

namespace App\Livewire\Public;

use App\Models\Certificate;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Livewire\Component;

class CertificateVerify extends Component
{
    public ?Certificate $certificate = null;

    public string $certificateId = '';

    public bool $notFound = false;

    public function mount(string $certificate_id)
    {
        $this->certificateId = $certificate_id;
        $this->certificate = Certificate::where('certificate_id', $certificate_id)
            ->with(['course', 'user'])
            ->first();

        if (! $this->certificate) {
            $this->notFound = true;
        }
    }

    public function generateQrCode(string $certificateId): string
    {
        $verificationUrl = route('certificates.verify', ['certificate_id' => $certificateId]);

        $qrCode = QrCode::create($verificationUrl)
            ->setSize(200)
            ->setMargin(10)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High);

        $writer = new SvgWriter;
        $result = $writer->write($qrCode);

        return $result->getString();
    }

    public function render()
    {
        return view('livewire.public.certificate-verify')->layout('layouts.app');
    }
}
