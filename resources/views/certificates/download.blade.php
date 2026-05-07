<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_id }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .certificate {
            width: 100%;
            max-width: 800px;
            aspect-ratio: 1.414;
            background: white;
            border: 12px double #cbd5e1;
            border-radius: 8px;
            padding: 40px;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .certificate-inner {
            border: 3px solid #fbbf24;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 30px;
            text-align: center;
        }
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 24px;
            font-weight: 900;
            color: #1e40af;
            letter-spacing: 2px;
        }
        .title {
            font-size: 36px;
            font-weight: 900;
            color: #1e293b;
            letter-spacing: 4px;
            font-family: Georgia, serif;
        }
        .subtitle {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .certify-text {
            font-style: italic;
            color: #64748b;
            font-size: 16px;
        }
        .recipient-name {
            font-size: 36px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 10px;
            font-family: Georgia, serif;
        }
        .course-text {
            font-style: italic;
            color: #64748b;
            font-size: 16px;
        }
        .course-name {
            font-size: 24px;
            font-weight: 800;
            color: #1e40af;
            text-transform: uppercase;
        }
        .meta {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .meta-item {
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
        }
        .meta-duration {
            background: #f1f5f9;
            color: #475569;
        }
        .meta-score {
            background: #dcfce7;
            color: #166534;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-item {
            text-align: center;
        }
        .footer-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .footer-value {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 5px;
        }
        .footer-cert-id {
            font-family: monospace;
            font-size: 12px;
        }
        .qr-section {
            text-align: center;
        }
        .qr-section svg {
            width: 80px;
            height: 80px;
        }
        .qr-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
        }
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #1e40af;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .print-btn:hover {
            background: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-inner">
            <div>
                <div class="logo">
                    <span>🎓</span>
                    <span>KR Learn</span>
                </div>
                <div class="title">CERTIFICATE</div>
                <div class="subtitle">Of Completion</div>
            </div>
            
            <div>
                <p class="certify-text">This is to certify that</p>
                <div class="recipient-name">{{ $user->name }}</div>
                <p class="course-text">has successfully completed the course</p>
                <div class="course-name">{{ $course->title }}</div>
            </div>
            
            <div>
                <div class="meta">
                    <span class="meta-item meta-duration">⏱️ {{ $certificate->completed_at->diffInHours($certificate->created_at) ?? 4 }} hours</span>
                    <span class="meta-item meta-score">✅ Score: 100%</span>
                </div>
            </div>
            
            <div class="footer">
                <div class="footer-item">
                    <div class="footer-value">{{ $certificate->completed_at->format('M d, Y') }}</div>
                    <div class="footer-label">Date of Completion</div>
                </div>
                <div class="qr-section">
                    <div>{!! $qrSvg !!}</div>
                    <div class="qr-label">Scan to Verify</div>
                </div>
                <div class="footer-item">
                    <div class="footer-value footer-cert-id">{{ $certificate->certificate_id }}</div>
                    <div class="footer-label">Certificate ID</div>
                </div>
            </div>
        </div>
    </div>
    
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
</body>
</html>
