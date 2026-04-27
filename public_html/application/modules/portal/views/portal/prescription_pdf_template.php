<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$rx_date = !empty($rx->date) ? date('d M Y', (int) $rx->date) : (!empty($rx->created_at) ? date('d M Y', strtotime($rx->created_at)) : 'Not specified');
$follow_up = !empty($rx->follow_up_date) ? date('d M Y', strtotime($rx->follow_up_date)) : 'Not specified';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { color: #1a1a1a; font-family: sans-serif; font-size: 13px; margin: 0; padding: 20px; }
h1 { font-size: 20px; font-weight: 700; margin: 4px 0; }
h2 { font-size: 14px; font-weight: 700; margin: 12px 0 6px; }
.header { display: flex; justify-content: space-between; margin-bottom: 16px; }
.muted { color: #666; font-size: 12px; }
.kicker { color: #0f766e; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
hr { border: none; border-top: 1px solid #ddd; margin: 14px 0; }
table { border-collapse: collapse; margin-bottom: 12px; width: 100%; }
th { background: #f6f8fb; border-bottom: 1px solid #ddd; color: #666; font-size: 11px; font-weight: 700; letter-spacing: .04em; padding: 6px 8px; text-align: left; text-transform: uppercase; }
td { border-bottom: 1px solid #eee; font-size: 13px; padding: 6px 8px; }
.advice { background: #f0fdf9; border-left: 3px solid #0f766e; line-height: 1.6; margin-top: 8px; padding: 10px 14px; }
.followup { color: #666; font-size: 12px; margin-top: 10px; }
.meta { text-align: right; }
</style>
</head>
<body>

<div class="header">
    <div>
        <div class="kicker">Prescription</div>
        <h1><?php echo htmlspecialchars($rx->doctor_name ?? 'Doctor', ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="muted"><?php echo htmlspecialchars($rx->doctor_degree ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
    <div class="meta">
        <div><strong>Patient:</strong> <?php echo htmlspecialchars($patient->name ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        <div><strong>Date:</strong> <?php echo htmlspecialchars($rx_date, ENT_QUOTES, 'UTF-8'); ?></div>
        <div><strong>Rx #:</strong> <?php echo (int) $rx_id; ?></div>
    </div>
</div>
<hr>

<h2>Medicines</h2>
<?php if (!empty($medicines)) : ?>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Medicine</th>
            <th>Dose</th>
            <th>Frequency</th>
            <th>Duration</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($medicines as $i => $med) : ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td>
                <?php echo htmlspecialchars($med['name'], ENT_QUOTES, 'UTF-8'); ?>
                <?php if (!empty($med['instruction'])) : ?>
                    <br><span class="muted"><?php echo htmlspecialchars($med['instruction'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($med['dose'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($med['frequency'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($med['days'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else : ?>
    <p class="muted">No medicines prescribed.</p>
<?php endif; ?>

<h2>Advice</h2>
<div class="advice">
    <?php echo !empty($rx->advice)
        ? nl2br(htmlspecialchars($rx->advice, ENT_QUOTES, 'UTF-8'))
        : 'No specific advice.'; ?>
</div>

<div class="followup">
    Follow-up:
    <?php echo htmlspecialchars($follow_up, ENT_QUOTES, 'UTF-8'); ?>
</div>

</body>
</html>
