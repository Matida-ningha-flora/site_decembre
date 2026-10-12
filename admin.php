<?php
/* ============================================================
   admin.php — Tableau de bord des Inscriptions
   Croisade Que Ton Règne Vienne
   ============================================================ */

require_once __DIR__ . '/php/config.php';

// Export CSV / Excel
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    ensureTable();
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM inscriptions ORDER BY created_at DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=inscriptions_croisade_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

    fputcsv($output, ['ID', 'Nom', 'Prénom', 'Téléphone', 'Ville', 'Église', 'Rôle', 'Attentes', 'Groupe WhatsApp', 'Date']);

    foreach ($rows as $r) {
        fputcsv($output, [
            $r['id'] ?? '',
            $r['nom'] ?? '',
            $r['prenom'] ?? '',
            $r['telephone'] ?? '',
            $r['ville'] ?? '',
            $r['eglise'] ?? '',
            $r['participation'] ?? '',
            $r['message'] ?? '',
            $r['groupe_whatsapp'] ?? '',
            $r['created_at'] ?? ''
        ]);
    }
    fclose($output);
    exit;
}

// Export Word (.doc)
if (isset($_GET['export']) && $_GET['export'] === 'word') {
    ensureTable();
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM inscriptions ORDER BY created_at DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header("Content-Type: application/vnd.ms-word; charset=utf-8");
    header("Content-Disposition: attachment; filename=inscriptions_croisade_" . date('Y-m-d') . ".doc");
    header("Cache-Control: max-age=0");

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="utf-8"><title>Inscriptions Croisade QTRV</title>';
    echo '<style>
        body { font-family: "Segoe UI", Calibri, Arial, sans-serif; font-size: 11pt; color: #1e293b; margin: 24px; }
        h1 { color: #E84C0E; font-size: 18pt; margin-bottom: 4px; font-weight: bold; }
        .sub { color: #64748b; font-size: 10pt; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 10pt; text-align: left; vertical-align: top; }
        th { background-color: #f1f5f9; color: #0f172a; font-weight: bold; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .badge { font-weight: bold; color: #0369a1; background: #e0f2fe; padding: 2px 6px; border-radius: 4px; font-size: 9pt; }
    </style></head><body>';
    echo '<h1>🔥 Croisade Que Ton Règne Vienne — Inscriptions</h1>';
    echo '<p class="sub">Document officiel généré le ' . date('d/m/Y à H:i') . ' — Total : ' . count($rows) . ' participant(s)</p>';
    echo '<table><thead><tr>
        <th style="width:5%;">#</th>
        <th style="width:16%;">Nom & Prénom</th>
        <th style="width:12%;">Téléphone</th>
        <th style="width:10%;">Ville</th>
        <th style="width:14%;">Église</th>
        <th style="width:10%;">Rôle</th>
        <th style="width:15%;">Attentes</th>
        <th style="width:10%;">Groupe</th>
        <th style="width:8%;">Date</th>
    </tr></thead><tbody>';
    foreach ($rows as $r) {
        echo '<tr>';
        echo '<td><b>#' . htmlspecialchars($r['id']) . '</b></td>';
        echo '<td><b>' . htmlspecialchars($r['nom'] . ' ' . $r['prenom']) . '</b></td>';
        echo '<td>' . htmlspecialchars($r['telephone'] ?: '—') . '</td>';
        echo '<td>' . htmlspecialchars($r['ville'] ?: '—') . '</td>';
        echo '<td>' . htmlspecialchars($r['eglise'] ?: '—') . '</td>';
        echo '<td><span class="badge">' . htmlspecialchars($r['participation'] ?: '—') . '</span></td>';
        echo '<td>' . (!empty($r['message']) ? nl2br(htmlspecialchars($r['message'])) : '—') . '</td>';
        echo '<td>' . (!empty($r['groupe_whatsapp']) ? nl2br(htmlspecialchars($r['groupe_whatsapp'])) : '—') . '</td>';
        echo '<td>' . date('d/m/Y H:i', strtotime($r['created_at'])) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></body></html>';
    exit;
}

ensureTable();
$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM inscriptions ORDER BY created_at DESC");
$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($inscriptions);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — Inscriptions Croisade QTRV</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- html2pdf.js for client-side PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary: #E84C0E;
            --dark: #0f172a;
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e2e8f0;
            --text: #1e293b;
            --text-muted: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            padding: 24px;
        }
        .container { max-width: 1350px; margin: 0 auto; }
        
        /* Barre d'administration (Boutons d'action) */
        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            background: var(--card);
            padding: 20px 24px;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .title-wrap h1 { font-size: 20px; font-weight: 800; color: var(--dark); }
        .title-wrap p { color: var(--text-muted); font-size: 13px; margin-top: 4px; }
        .badge-count {
            background: #ffedd5;
            color: var(--primary);
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-block;
        }
        .actions-wrap {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: white;
            padding: 9px 15px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-excel { background: #107c41; }
        .btn-word { background: #185abd; }
        .btn-pdf { background: #c4302b; }
        .btn-secondary {
            background: #f1f5f9;
            color: var(--dark);
            border: 1px solid var(--border);
        }

        /* Document imprimable / exportable */
        .printable-document {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .doc-header {
            padding: 20px 24px;
            background: #ffffff;
            border-bottom: 2px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .doc-header h2 {
            font-size: 18px;
            font-weight: 800;
            color: var(--dark);
        }
        .doc-meta {
            font-size: 13px;
            color: var(--text-muted);
        }
        .doc-meta strong {
            color: var(--primary);
        }

        /* Tableau */
        .table-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }
        th {
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 700;
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }
        .badge-role {
            background: #e0f2fe;
            color: #0369a1;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .date { color: var(--text-muted); font-size: 12px; white-space: nowrap; }
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        /* Printable PDF / Print CSS */
        @media print {
            body { background: white; padding: 0; color: black; }
            .no-print { display: none !important; }
            .printable-document { box-shadow: none; border: none; }
            .doc-header { padding: 10px 0; border-bottom: 2px solid #E84C0E; }
            table { font-size: 11px; width: 100%; }
            th, td { padding: 6px 8px; border: 1px solid #cbd5e1; }
            th { background: #f1f5f9 !important; color: black; }
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Barre de contrôle (Mode écran uniquement - EXCLUE DU FICHIER TÉLÉCHARGÉ) -->
        <header class="admin-nav no-print">
            <div class="title-wrap">
                <h1>🔥 Administration — Croisade QTRV</h1>
                <p>Gestion locale de la base de données (<code>data.db</code>)</p>
            </div>
            <div class="actions-wrap">
                <span class="badge-count"><?= $total ?> inscription<?= $total > 1 ? 's' : '' ?></span>
                <a href="?export=csv" class="btn btn-excel" title="Télécharger au format Excel (CSV)">📊 Excel (CSV)</a>
                <a href="?export=word" class="btn btn-word" title="Télécharger au format Microsoft Word">📄 Word (.doc)</a>
                <button type="button" id="btnPdf" class="btn btn-pdf" onclick="exportPDF()" title="Télécharger le fichier PDF">📕 PDF</button>
                <button type="button" class="btn btn-secondary" onclick="window.print()" title="Imprimer ou enregistrer au format PDF navigateur">🖨️ Imprimer</button>
                <a href="index.html" class="btn btn-secondary">← Retour au site</a>
            </div>
        </header>

        <!-- Document officiel (Seul élément capturé dans le PDF et le Word) -->
        <main id="printableArea" class="printable-document">
            
            <div class="doc-header">
                <div>
                    <h2>🔥 Croisade Que Ton Règne Vienne — Liste des Inscriptions</h2>
                </div>
                <div class="doc-meta">
                    Généré le <?= date('d/m/Y à H:i') ?> — <strong>Total : <?= $total ?> inscrit<?= $total > 1 ? 's' : '' ?></strong>
                </div>
            </div>

            <div class="table-wrap">
                <?php if ($total === 0): ?>
                    <div class="empty-state">
                        <h3>Aucune inscription enregistrée pour le moment.</h3>
                        <p style="margin-top:8px;">Remplissez le formulaire sur la page principale pour tester !</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:4%;">#</th>
                                <th style="width:16%;">Nom & Prénom</th>
                                <th style="width:12%;">Téléphone</th>
                                <th style="width:10%;">Ville</th>
                                <th style="width:12%;">Église</th>
                                <th style="width:10%;">Rôle</th>
                                <th style="width:18%;">Attentes</th>
                                <th style="width:10%;">Groupe</th>
                                <th style="width:8%;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inscriptions as $i): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($i['id']) ?></strong></td>
                                    <td><strong><?= htmlspecialchars($i['nom'] . ' ' . $i['prenom']) ?></strong></td>
                                    <td><a href="tel:<?= htmlspecialchars($i['telephone']) ?>" style="color:var(--primary); text-decoration:none; font-weight:600;"><?= htmlspecialchars($i['telephone'] ?: '—') ?></a></td>
                                    <td><?= htmlspecialchars($i['ville'] ?: '—') ?></td>
                                    <td><?= htmlspecialchars($i['eglise'] ?: '—') ?></td>
                                    <td><span class="badge-role"><?= htmlspecialchars($i['participation'] ?: 'Non précisé') ?></span></td>
                                    <td style="max-width:240px; word-break:break-word; line-height:1.4;"><?= !empty($i['message']) ? nl2br(htmlspecialchars($i['message'])) : '—' ?></td>
                                    <td style="max-width:200px; word-break:break-word; line-height:1.4;"><?= !empty($i['groupe_whatsapp']) ? nl2br(htmlspecialchars($i['groupe_whatsapp'])) : '—' ?></td>
                                    <td class="date"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($i['created_at']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function exportPDF() {
            const btn = document.getElementById('btnPdf');
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳ Génération...';
            btn.style.opacity = '0.7';
            btn.disabled = true;

            // Seul le document officiel (tableau + en-tête officiel) est capturé,
            // la barre de boutons (admin-nav) n'en fait pas partie !
            const element = document.getElementById('printableArea');
            const opt = {
                margin:       [8, 8, 8, 8],
                filename:     'inscriptions_croisade_' + new Date().toISOString().slice(0, 10) + '.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            if (window.html2pdf) {
                html2pdf().set(opt).from(element).save().then(() => {
                    btn.innerHTML = originalText;
                    btn.style.opacity = '1';
                    btn.disabled = false;
                }).catch(err => {
                    console.error('Erreur html2pdf:', err);
                    window.print();
                    btn.innerHTML = originalText;
                    btn.style.opacity = '1';
                    btn.disabled = false;
                });
            } else {
                window.print();
                btn.innerHTML = originalText;
                btn.style.opacity = '1';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
