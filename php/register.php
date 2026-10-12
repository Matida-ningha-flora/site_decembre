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
$nom             = clean($_POST['nom']             ?? '');
$prenom          = clean($_POST['prenom']          ?? '');
$email           = clean($_POST['email']           ?? '');
$telephone       = clean($_POST['telephone']       ?? '');
$ville           = clean($_POST['ville']           ?? '');
$eglise          = clean($_POST['eglise']          ?? '');
$participation   = clean($_POST['participation']   ?? '');
$message         = clean($_POST['message']         ?? '');
$groupe_whatsapp = clean($_POST['groupe_whatsapp'] ?? '');

/* ─── Validation ─────────────────────────────────────────── */
$errors = [];

if (empty($nom))    $errors[] = 'Le nom est obligatoire.';
if (empty($prenom)) $errors[] = 'Le prénom est obligatoire.';

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Adresse email invalide.';
}

if (!empty($telephone) && !preg_match('/^[\d\s\+\-\(\)]{6,25}$/', $telephone)) {
    $errors[] = 'Numéro de téléphone invalide.';
}

if (strlen($nom)             > 100) $errors[] = 'Le nom est trop long.';
if (strlen($prenom)          > 100) $errors[] = 'Le prénom est trop long.';
if (strlen($eglise)          > 200) $errors[] = "Le nom d'église est trop long.";
if (strlen($message)         > 2000) $errors[] = 'Le message est trop long.';
if (strlen($groupe_whatsapp) > 500)  $errors[] = 'La réponse au groupe est trop longue.';

if (!empty($errors)) {
    respond(false, implode(' ', $errors), 422);
}

/* ─── Enregistrement dans la base de données ─────────────── */
try {
    ensureTable();
    $pdo = getDB();

    $createdAt = date('Y-m-d H:i:s');

    /* ─── Insertion ─────────────────────────────────────────── */
    $insertStmt = $pdo->prepare("
        INSERT INTO inscriptions
            (nom, prenom, email, telephone, ville, eglise, participation, message, groupe_whatsapp, created_at)
        VALUES
            (:nom, :prenom, :email, :telephone, :ville, :eglise, :participation, :message, :groupe_whatsapp, :created_at)
    ");

    $insertStmt->execute([
        ':nom'             => $nom,
        ':prenom'          => $prenom,
        ':email'           => $email           ?: null,
        ':telephone'       => $telephone       ?: null,
        ':ville'           => $ville           ?: null,
        ':eglise'          => $eglise          ?: null,
        ':participation'   => $participation   ?: null,
        ':message'         => $message         ?: null,
        ':groupe_whatsapp' => $groupe_whatsapp ?: null,
        ':created_at'      => $createdAt,
    ]);

    $insertId = $pdo->lastInsertId();

    respond(true, "🙏 Inscription confirmée ! Bienvenue, {$prenom}. Que Dieu vous bénisse abondamment !");

} catch (Throwable $e) {
    // Log l'erreur en interne
    error_log('[Croisade Inscription Error] ' . $e->getMessage());
    respond(false, 'Une erreur technique est survenue. Veuillez réessayer ou nous contacter directement.', 500);
}
