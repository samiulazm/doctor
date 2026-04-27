<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$rx_date = !empty($rx->date) ? date('d M Y', (int) $rx->date) : (!empty($rx->created_at) ? date('d M Y', strtotime($rx->created_at)) : 'Not specified');
$follow_up = !empty($rx->follow_up_date) ? date('d M Y', strtotime($rx->follow_up_date)) : 'Not specified';
?>

<div class="container chamber-public-container py-4">
    <div class="chamber-public-panel chamber-rx-doc">
        <div class="chamber-rx-header">
            <div class="chamber-rx-doctor">
                <div class="chamber-kicker">Prescription</div>
                <h1 class="chamber-rx-name">
                    <?php echo htmlspecialchars($rx->doctor_name ?? 'Doctor', ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <p class="chamber-muted mb-0">
                    <?php echo htmlspecialchars($rx->doctor_degree ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
            <div class="chamber-rx-meta text-right">
                <p class="mb-1"><strong>Patient:</strong>
                    <?php echo htmlspecialchars($patient->name ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <p class="mb-1"><strong>Date:</strong>
                    <?php echo htmlspecialchars($rx_date, ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <p class="mb-0"><strong>Rx #:</strong> <?php echo (int) $rx_id; ?></p>
            </div>
        </div>

        <hr class="chamber-rx-divider">

        <h2 class="chamber-panel-title mb-3">
            <i class="fas fa-pills mr-1 text-info"></i> Medicines
        </h2>

        <?php if (!empty($medicines)) : ?>
        <table class="table chamber-table chamber-rx-table">
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
                            <div class="chamber-muted small"><?php echo htmlspecialchars($med['instruction'], ENT_QUOTES, 'UTF-8'); ?></div>
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
            <p class="chamber-muted">No medicines prescribed.</p>
        <?php endif; ?>

        <div class="chamber-rx-advice mt-4">
            <h2 class="chamber-panel-title mb-2">
                <i class="fas fa-comment-medical mr-1 text-success"></i> Advice
            </h2>
            <div class="chamber-rx-advice-text">
                <?php echo !empty($rx->advice)
                    ? nl2br(htmlspecialchars($rx->advice, ENT_QUOTES, 'UTF-8'))
                    : 'No specific advice.'; ?>
            </div>
        </div>

        <div class="chamber-rx-followup mt-3 chamber-muted small">
            <i class="fas fa-redo mr-1"></i> Follow-up:
            <?php echo htmlspecialchars($follow_up, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    </div>

    <div class="chamber-btn-row justify-content-center mt-3">
        <a href="<?php echo site_url('portal/prescription_pdf/' . (int) $rx_id); ?>" class="btn btn-primary">
            <i class="fas fa-download mr-1"></i> Download PDF
        </a>
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>
