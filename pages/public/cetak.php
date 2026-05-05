<?php
global $url_parts, $pdo;
$student_id = isset($url_parts[1]) ? intval($url_parts[1]) : 0;

if ($student_id <= 0) {
    echo "ID Siswa tidak valid.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute(['id' => $student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "Siswa tidak ditemukan.";
    exit;
}

if ($student['status'] !== 'LULUS') {
    echo "Maaf, Surat Keterangan Kelulusan hanya tersedia untuk siswa yang dinyatakan LULUS.";
    exit;
}

if (isset($student['is_locked']) && $student['is_locked'] == 1) {
    echo "Maaf, Status SKL Anda ditangguhkan/dikunci sementara. Silakan hubungi bagian Administrasi Sekolah untuk informasi lebih lanjut.";
    exit;
}

$school_name = get_setting('school_name');
$school_address = get_setting('school_address');
$principal_name = get_setting('principal_name');
$principal_nip = get_setting('principal_nip');
$letter_header = get_setting('letter_header');

// Security Check: Unique Verification Code / Hash
$hash_salt = "PORTAL_SMK_5_AGUSTUS_SECURE_SALT_2026";
$verification_hash = strtoupper(substr(hash('sha256', $student['id'] . $student['exam_number'] . $student['nisn'] . $hash_salt), 0, 16));

// Document letter numbering logic
$roman_months = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
$month_num = intval(date('n'));
$roman_month = isset($roman_months[$month_num]) ? $roman_months[$month_num] : 'V';
$current_year = date('Y');
$padded_id = str_pad($student['id'], 3, '0', STR_PAD_LEFT);
$document_number = "{$padded_id}/SKL/SMK.5.AGS/{$roman_month}/{$current_year}";

// Security Check: URL generation for the QR code
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$verify_url = $protocol . $_SERVER['HTTP_HOST'] . "/kelulusan/verifikasi/" . $student['id'];
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($verify_url);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kelulusan - <?php echo sanitize($student['name']); ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            background-color: #ffffff;
            margin: 0;
            padding: 12px;
            font-size: 10.5pt;
            line-height: 1.35;
            position: relative;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .letter-container {
            max-width: 780px;
            margin: 0 auto;
            padding: 5px;
            position: relative;
        }

        /* Anti-Forgery Document Watermark */
        .watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 38pt;
            color: rgba(0, 0, 0, 0.04);
            white-space: nowrap;
            font-weight: 800;
            user-select: none;
            pointer-events: none;
            z-index: -1;
            text-align: center;
            border: 4px solid rgba(0, 0, 0, 0.03);
            padding: 8px 32px;
            letter-spacing: 2px;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .header h2 {
            margin: 0;
            font-size: 15pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            font-style: italic;
        }

        .title {
            text-align: center;
            margin-bottom: 12px;
        }

        .title h3 {
            margin: 0;
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .title p {
            margin: 2px 0 0 0;
            font-size: 10pt;
        }

        .content {
            margin-bottom: 10px;
            text-align: justify;
        }

        .data-table {
            width: 88%;
            margin: 8px auto 12px auto;
            border-collapse: collapse;
        }

        .data-table td {
            padding: 3px 6px;
            vertical-align: top;
        }

        .data-table td:first-child {
            width: 32%;
        }

        .data-table td:nth-child(2) {
            width: 3%;
        }

        /* Footer Alignment for QR Code and Signature */
        .footer-layout {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .qr-wrapper {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 7.5pt;
            color: #475569;
            max-width: 250px;
        }

        .qr-wrapper img {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 3px;
            background: #fff;
        }

        .signature {
            width: 240px;
            text-align: left;
        }

        .signature .date {
            margin-bottom: 4px;
        }

        .signature .name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 4px;
        }

        /* Bottom Security Details */
        .security-footer {
            margin-top: 24px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
            font-size: 7.5pt;
            font-family: sans-serif;
            color: #64748b;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            body {
                padding: 10px;
                margin: 0;
                font-size: 10pt;
            }
            .letter-container {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .watermark {
                color: rgba(0, 0, 0, 0.04);
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 800px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; background: #f1f5f9; padding: 12px 20px; border-radius: 8px; font-family: sans-serif;">
        <span style="font-size: 14px; font-weight: 600;">🖨️ Pratinjau Surat Kelulusan</span>
        <button onclick="window.print()" style="background: #2563eb; color: #fff; padding: 8px 16px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Cetak Sekarang</button>
    </div>

    <div class="letter-container">
        <!-- Anti-Forgery Watermark Overlay -->
        <div class="watermark">DOKUMEN ASLI & SAH</div>

        <!-- Header / Kop Surat -->
        <div class="header">
            <h2>PEMERINTAH PROVINSI RIAU</h2>
            <h2>DINAS PENDIDIKAN</h2>
            <h2 style="font-size: 18pt;"><?php echo sanitize($school_name); ?></h2>
            <p><?php echo sanitize($school_address); ?></p>
        </div>

        <!-- Title Surat -->
        <div class="title">
            <h3><?php echo sanitize($letter_header); ?></h3>
            <p>Nomor: <?php echo $document_number; ?></p>
        </div>

        <!-- Opening and Content -->
        <div class="content">
            <p>Yang bertanda tangan di bawah ini, Kepala Sekolah <?php echo sanitize($school_name); ?>, menerangkan bahwa:</p>
            
            <table class="data-table">
                <tr>
                    <td>Nama Lengkap</td>
                    <td>:</td>
                    <td><strong><?php echo sanitize($student['name']); ?></strong></td>
                </tr>
                <tr>
                    <td>Nomor Peserta Ujian</td>
                    <td>:</td>
                    <td><?php echo sanitize($student['exam_number']); ?></td>
                </tr>
                <tr>
                    <td>NISN</td>
                    <td>:</td>
                    <td><?php echo sanitize($student['nisn']); ?></td>
                </tr>
                <tr>
                    <td>Kompetensi Keahlian</td>
                    <td>:</td>
                    <td><?php echo sanitize($student['major']); ?></td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td><?php echo sanitize($student['class_name']); ?></td>
                </tr>
            </table>

            <p style="margin-bottom: 12px;">Berdasarkan kriteria kelulusan peserta didik yang ditetapkan oleh sekolah serta hasil rapat pleno dewan guru, maka nama siswa yang tercantum di atas dinyatakan:</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <span style="font-size: 16pt; font-weight: bold; border: 2px solid #000; padding: 6px 40px; text-transform: uppercase;">
                    L U L U S
                </span>
            </div>

            <!-- Subject Scores Table -->
            <?php
            $existing_scores = !empty($student['subject_scores']) ? json_decode($student['subject_scores'], true) : [];
            if (is_array($existing_scores) && count($existing_scores) > 0):
            ?>
            <div style="margin-top: 10px; margin-bottom: 12px;">
                <p style="font-weight: bold; margin-bottom: 4px; font-size: 9.5pt;">Daftar Nilai Mata Pelajaran:</p>
                <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
                    <thead>
                        <tr style="background-color: #f8fafc; border: 1px solid #cbd5e1;">
                            <th style="padding: 4px 8px; text-align: left; border: 1px solid #cbd5e1; width: 40px;">No.</th>
                            <th style="padding: 4px 8px; text-align: left; border: 1px solid #cbd5e1;">Mata Pelajaran</th>
                            <th style="padding: 4px 8px; text-align: center; border: 1px solid #cbd5e1; width: 100px;">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $n = 1; 
                        $total_val = 0;
                        $count_val = 0;
                        foreach ($existing_scores as $sub => $val): 
                            if (!empty($val)) {
                                $total_val += (float)$val;
                                $count_val++;
                            }
                        ?>
                        <tr style="border: 1px solid #cbd5e1;">
                            <td style="padding: 3px 8px; border: 1px solid #cbd5e1; text-align: center;"><?php echo $n++; ?></td>
                            <td style="padding: 3px 8px; border: 1px solid #cbd5e1;"><?php echo sanitize($sub); ?></td>
                            <td style="padding: 3px 8px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold;"><?php echo !empty($val) ? number_format((float)$val, 2) : '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if ($count_val > 0): 
                            $avg_score = $total_val / $count_val;
                        ?>
                        <tr style="border: 1px solid #cbd5e1; background-color: #f8fafc; font-weight: bold;">
                            <td style="padding: 4px 10px; border: 1px solid #cbd5e1; text-align: right;" colspan="2">Nilai Rata-rata</td>
                            <td style="padding: 4px 10px; border: 1px solid #cbd5e1; text-align: center;"><?php echo number_format($avg_score, 2); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <p>Demikian surat keterangan ini diberikan agar dapat dipergunakan seperlunya dan sebagai bukti sementara kelulusan sebelum ijazah resmi diterbitkan.</p>
        </div>

        <!-- Dual Signature and QR Validation Area -->
        <div class="footer-layout">
            <!-- QR Verification notice on left -->
            <div class="qr-wrapper">
                <div style="font-size: 9pt; line-height: 1.4; color: #334155;">
                    <strong>Catatan Penting:</strong><br>
                    Dokumen ini sah dan diterbitkan secara elektronik oleh <strong><?php echo sanitize($school_name); ?></strong>. Tanda tangan yang tertera di sebelah kanan adalah tanda tangan digital resmi yang divalidasi melalui sistem.
                </div>
            </div>

            <!-- Signature with QR code on right -->
            <div class="signature">
                <div class="date">Pekanbaru, <?php echo date('d F Y'); ?></div>
                <div style="margin-bottom: 8px;">Kepala Sekolah,</div>
                <div style="margin-bottom: 8px;">
                    <img src="<?php echo $qr_code_url; ?>" alt="QR Signature" width="105" height="105" style="border: 1px solid #cbd5e1; padding: 4px; background: #fff;">
                    <div style="font-size: 7.5pt; color: #64748b; font-family: sans-serif; margin-top: 2px; font-weight: bold; letter-spacing: 0.5px;">TANDA TANGAN DIGITAL</div>
                </div>
                <div class="name"><?php echo sanitize($principal_name); ?></div>
                <div>NIP. <?php echo sanitize($principal_nip); ?></div>
            </div>
        </div>

        <!-- Security footer containing Verification ID -->
        <div class="security-footer">
            <span>Valid ID: <strong>#<?php echo $verification_hash; ?></strong></span>
            <span>Portal Informasi Kelulusan Resmi SMK 5 AGUSTUS PEKANBARU</span>
        </div>
    </div>

    <!-- Advanced Document Anti-Manipulation Protection -->
    <script>
        // 1. Block right-click context menu
        document.addEventListener('contextmenu', e => e.preventDefault());

        // 2. Block Inspect shortcuts (F12, Ctrl+Shift+I/J/C, Ctrl+U)
        document.addEventListener('keydown', e => {
            if (
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
                (e.ctrlKey && (e.key === 'U' || e.key === 'S' || e.key === 'P'))
            ) {
                e.preventDefault();
                alert('Aksi ini tidak diizinkan demi keamanan dokumen.');
            }
        });

        // 3. DOM Mutation Protection (Reloads page instantly if content is altered using DevTools)
        const targetNode = document.querySelector('.letter-container');
        if (targetNode) {
            const observer = new MutationObserver((mutationsList, observer) => {
                alert('Terdeteksi upaya manipulasi data. Halaman akan dimuat ulang.');
                window.location.reload();
            });
            observer.observe(targetNode, {
                attributes: true,
                childList: true,
                subtree: true,
                characterData: true
            });
        }
    </script>
</body>
</html>
