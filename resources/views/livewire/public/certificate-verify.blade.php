@php
    use Endroid\QrCode\Color\Color;
    use Endroid\QrCode\QrCode;
    use Endroid\QrCode\Writer\SvgWriter;
    
    $qrCode = null;
    $qrSvg = null;
    if ($certificate) {
        $verificationUrl = route('certificates.verify', ['certificate_id' => $certificate->certificate_id]);
        $qrCode = new QrCode($verificationUrl);
        $qrCode->setSize(200);
        $qrCode->setMargin(10);
        $qrCode->setForegroundColor(new Color(0, 0, 0));
        $qrCode->setBackgroundColor(new Color(255, 255, 255));
        $writer = new SvgWriter();
        $qrSvg = $writer->write($qrCode)->getString();
    }
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        @if($notFound)
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-red-200 dark:border-red-900/50 p-12 shadow-xl text-center">
                <div class="h-24 w-24 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-6">
                    <flux:icon.x-circle class="h-12 w-12 text-red-600 dark:text-red-400" />
                </div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100 mb-4">Certificate Not Found</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-lg">The certificate ID "{{ $certificateId }}" could not be verified.</p>
                <p class="text-zinc-400 dark:text-zinc-500 mt-4">Please check the certificate ID and try again.</p>
            </div>
        @elseif($certificate)
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center">
                                <flux:icon.check-badge class="h-7 w-7 text-white" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">Certificate Verified</h1>
                                <p class="text-blue-100 text-sm">This certificate is authentic and valid</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                                <p class="text-white font-bold text-lg">{{ $certificate->certificate_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Certificate Details --}}
                <div class="p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        {{-- Left: Certificate Info --}}
                        <div class="space-y-6">
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Recipient</label>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ $certificate->user->name }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Course</label>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $certificate->course->title }}</p>
                            </div>

                            @if($certificate->course->description)
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Description</label>
                                    <p class="text-zinc-600 dark:text-zinc-300 mt-1">{{ $certificate->course->description }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Issue Date</label>
                                    <p class="text-zinc-900 dark:text-zinc-100 font-semibold mt-1">
                                        {{ $certificate->issued_at ? $certificate->issued_at->format('M d, Y') : $certificate->completed_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Completion Date</label>
                                    <p class="text-zinc-900 dark:text-zinc-100 font-semibold mt-1">
                                        {{ $certificate->completed_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Right: QR Code --}}
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-white p-4 rounded-2xl border-2 border-zinc-100 dark:border-zinc-800 shadow-inner">
                                {!! $qrSvg !!}
                            </div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-4 text-center">
                                Scan to verify this certificate
                            </p>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                                {{ $certificate->certificate_id }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-zinc-50 dark:bg-zinc-800/50 px-8 py-4 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center justify-center gap-2">
                        <flux:icon.academic-cap class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        <span class="font-bold text-zinc-600 dark:text-zinc-300">KR Learn</span>
                        <span class="text-zinc-400 dark:text-zinc-500">| Certificate Verification System</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
