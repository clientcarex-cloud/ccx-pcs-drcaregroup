<!DOCTYPE html>
<html>
<head>
    <title>Prescription</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body onload="window.print()">
    <h3>Prescription Details</h3>
    <p><strong>Code:</strong> <?= $prescription['prescription_code'] ?></p>
    <p><strong>Date:</strong> <?= date('d-m-Y', strtotime($prescription['created_at'])) ?></p>
    <p><strong>Doctor:</strong> <?= $prescription['doctor_name'] ?></p>
    <p><strong>Notes:</strong> <?= $prescription['notes'] ?></p>

    <h4>Medicines</h4>
    <table>
        <thead>
            <tr>
                <th>Medicine</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Usage</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prescription['medicines'] as $med): ?>
                <tr>
                    <td><?= $med['medicine_name'] ?></td>
                    <td><?= $med['frequency'] ?></td>
                    <td><?= $med['duration'] ?> Days</td>
                    <td><?= $med['usage'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
