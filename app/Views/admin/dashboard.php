<div class="section-head">
    <h1>Admin overview</h1>
    <a class="btn btn-primary" href="<?= url('/admin/papers/create') ?>">Upload paper</a>
</div>

<div class="stats-grid">
    <div class="stat-card"><span>Students</span><strong><?= (int)$stats['students'] ?></strong></div>
    <div class="stat-card"><span>Papers</span><strong><?= (int)$stats['papers'] ?></strong></div>
    <div class="stat-card"><span>Purchases</span><strong><?= (int)$stats['purchases'] ?></strong></div>
    <div class="stat-card"><span>Revenue</span><strong><?= money($stats['revenue']) ?></strong></div>
    <div class="stat-card"><span>Downloads</span><strong><?= (int)$stats['downloads'] ?></strong></div>
    <div class="stat-card"><span>Published</span><strong><?= (int)$stats['published_papers'] ?></strong></div>
</div>

<div class="split-panels">
    <div>
        <h2>Recent payments</h2>
        <?php if ($stats['recent_payments'] === []): ?>
            <div class="empty-state"><p>No payments yet.</p></div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Paper</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['recent_payments'] as $payment): ?>
                            <tr>
                                <td>
                                    <?= e($payment['user_name']) ?>
                                    <div class="muted"><?= e($payment['user_email']) ?></div>
                                </td>
                                <td><?= e($payment['paper_title']) ?></td>
                                <td><?= money($payment['amount']) ?></td>
                                <td><span class="badge"><?= e($payment['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <h2>New students</h2>
        <ul class="activity-list">
            <?php foreach ($stats['recent_students'] as $student): ?>
                <li>
                    <strong><?= e($student['name']) ?></strong>
                    <span class="muted"><?= e($student['email']) ?> · <?= e(date('M j, Y', strtotime($student['created_at']))) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
