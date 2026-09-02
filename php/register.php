<?php
/* ============================================================
   register.php — Inscription Handler
   Croisade Que Ton Règne Vienne
   ============================================================ */

require_once __DIR__ . '/config.php';

/* ─── Headers ────────────────────────────────────────────── */
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

/* ─── Méthode ────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

/* ─── Helpers ────────────────────────────────────────────── */
function clean(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

function respond(bool $success, string $message, int $code = 200): void {
    http_response_code($code);
    echo json_encode(['success' => $success, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ─── Récupération des champs ────────────────────────────── */
$nom           = clean($_POST['nom']           ?? '');
$prenom        = clean($_POST['prenom']        ?? '');
$telephone     = clean($_POST['telephone']     ?? '');
$ville         = clean($_POST['ville']         ?? '');
$eglise        = clean($_POST['eglise']        ?? '');
$participation = clean($_POST['participation'] ?? '');
$message       = clean($_POST['message']       ?? '');

/* ─── Validation ─────────────────────────────────────────── */
$errors = [];

if (empty($nom))    $errors[] = 'Le nom est obligatoire.';
if (empty($prenom)) $errors[] = 'Le prénom est obligatoire.';

if (!empty($telephone) && !preg_match('/^[\d\s\+\-\(\)]{6,20}$/', $telephone)) {
    $errors[] = 'Numéro de téléphone invalide.';
}

if (strlen($nom)    > 100) $errors[] = 'Le nom est trop long.';
if (strlen($prenom) > 100) $errors[] = 'Le prénom est trop long.';
if (strlen($eglise) > 200) $errors[] = "Le nom d'église est trop long.";
if (strlen($message) > 2000) $errors[] = 'Le message est trop long.';

if (!empty($errors)) {
    respond(false, implode(' ', $errors), 422);
}

/* ─── Anti-spam : vérification doublons (même email + 24h) ── */
try {
    ensureTable();
    $pdo = getDB();

    /* ─── Insertion ─────────────────────────────────────────── */
    $insertStmt = $pdo->prepare("
        INSERT INTO inscriptions
            (nom, prenom, telephone, ville, eglise, participation, message, created_at)
        VALUES
            (:nom, :prenom, :telephone, :ville, :eglise, :participation, :message, NOW())
    ");

    $insertStmt->execute([
        ':nom'           => $nom,
        ':prenom'        => $prenom,
        ':telephone'     => $telephone ?: null,
        ':ville'         => $ville     ?: null,
        ':eglise'        => $eglise    ?: null,
        ':participation' => $participation ?: null,
        ':message'       => $message   ?: null,
    ]);

    $insertId = $pdo->lastInsertId();

    /* ─── (Optionnel) Envoi email de confirmation ────────────
       Décommentez et configurez si vous avez PHP mail() disponible

    $to      = $email;
    $subject = '✅ Confirmation d\'inscription — Croisade Que Ton Règne Vienne';
    $body    = "Bonjour {$prenom} {$nom},\n\n"
             . "Votre inscription a bien été enregistrée. Nous vous contacterons prochainement.\n\n"
             . "Jésus au centre de l'adoration 🔥\n"
             . "L'équipe Croisade Que Ton Règne Vienne";
    $headers = 'From: noreply@croisade-ctrv.cm';
    @mail($to, $subject, $body, $headers);
    ──────────────────────────────────────────────────────── */

    respond(true, "🙏 Inscription confirmée ! Bienvenue, {$prenom}. Que Dieu vous bénisse abondamment !");

} catch (PDOException $e) {
    // Log l'erreur en interne (ne pas exposer à l'utilisateur)
    error_log('[Croisade Inscription Error] ' . $e->getMessage());
    respond(false, 'Une erreur technique est survenue. Veuillez réessayer ou nous contacter directement.', 500);
}
