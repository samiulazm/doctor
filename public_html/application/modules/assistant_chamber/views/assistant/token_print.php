<?php defined('BASEPATH') OR exit('No direct script access allowed');
$practice_name = isset($settings->hospital_name) && $settings->hospital_name !== '' ? $settings->hospital_name : 'Chamber';
$doctor_name = '';
if ($doctor) {
    if (!empty($doctor->name)) {
        $doctor_name = $doctor->name;
    } else {
        $first = isset($doctor->first_name) ? $doctor->first_name : '';
        $last = isset($doctor->last_name) ? $doctor->last_name : '';
        $doctor_name = trim($first . ' ' . $last);
    }
}
$patient_name = !empty($queue_row->guest_name) ? $queue_row->guest_name : ('Patient #' . $queue_row->patient_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serial Token - #<?php echo (int) $queue_row->serial_number; ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #fff;
            color: #000;
            font-family: "Courier New", Courier, monospace;
            font-size: 13px;
            max-width: 300px;
            padding: 12px;
        }
        .practice-name {
            border-bottom: 1px dashed #000;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            padding-bottom: 6px;
            text-align: center;
        }
        .doctor-name {
            font-size: 12px;
            margin-bottom: 10px;
            text-align: center;
        }
        .serial-block {
            margin: 12px 0;
            text-align: center;
        }
        .serial-label {
            font-size: 12px;
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .serial-number {
            font-size: 48px;
            font-weight: bold;
            line-height: 1;
            margin: 4px 0;
        }
        .patient-row {
            border-top: 1px dashed #000;
            font-size: 12px;
            margin-top: 8px;
            padding-top: 8px;
        }
        .patient-row .label {
            color: #555;
            font-size: 10px;
            letter-spacing: .05em;
            text-transform: uppercase;
        }
        .footer {
            border-top: 1px dashed #000;
            color: #555;
            font-size: 10px;
            margin-top: 10px;
            padding-top: 6px;
            text-align: center;
        }
        @media print {
            body { margin: 0; padding: 8px; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="practice-name">
        <?php echo htmlspecialchars($practice_name, ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <?php if ($doctor_name !== '') : ?>
        <div class="doctor-name">
            Dr. <?php echo htmlspecialchars($doctor_name, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="serial-block">
        <div class="serial-label">Serial No.</div>
        <div class="serial-number"><?php echo (int) $queue_row->serial_number; ?></div>
    </div>

    <div class="patient-row">
        <div class="label">Patient</div>
        <div><?php echo htmlspecialchars($patient_name, ENT_QUOTES, 'UTF-8'); ?></div>
    </div>

    <div class="footer">
        <?php echo date('d M Y, h:i A'); ?>
    </div>
</body>
</html>
