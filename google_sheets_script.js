/**
 * ============================================================
 * SCRIPT GOOGLE APPS SCRIPT — INSCRIPTION CROISADE QTRV
 * ============================================================
 * Ce script reçoit les données du formulaire de votre site Vercel,
 * les ajoute automatiquement dans votre Google Sheet,
 * et vous envoie un email de notification en temps réel.
 * ============================================================
 *
 * ÉTAPE 1 : Créez une nouvelle feuille sur Google Sheets (https://sheets.new)
 * ÉTAPE 2 : Dans le menu, cliquez sur "Extensions" > "Apps Script"
 * ÉTAPE 3 : Supprimez tout le code et collez ce script
 * ÉTAPE 4 : Remplacez "VOTRE_EMAIL@GMAIL.COM" par votre adresse email
 * ÉTAPE 5 : Cliquez sur "Déployer" > "Nouveau déploiement"
 *           - Type : "Application Web"
 *           - Exécuter en tant que : "Moi"
 *           - Qui a accès : "Tout le monde" (IMPORTANT !)
 * ÉTAPE 6 : Copiez l'URL de l'application Web et collez-la dans js/main.js
 * ============================================================
 */

function doPost(e) {
  try {
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();

    // 1. Si la feuille est vide, créer automatiquement la ligne d'en-tête
    if (sheet.getLastRow() === 0) {
      sheet.appendRow([
        "Date & Heure",
        "Nom",
        "Prénom",
        "Téléphone",
        "Ville",
        "Église",
        "Rôle / Participation",
        "Attentes",
        "Groupe WhatsApp"
      ]);
      // Mise en forme de l'en-tête (gras, fond orangé)
      sheet.getRange(1, 1, 1, 9)
           .setFontWeight("bold")
           .setBackground("#FFE0B2")
           .setHorizontalAlignment("center");
      sheet.setFrozenRows(1);
    }

    // 2. Récupération des données envoyées par le formulaire
    var p = e.parameter || {};
    var dateStr = Utilities.formatDate(new Date(), "GMT+1", "dd/MM/yyyy HH:mm:ss");
    var nom = p.nom || "";
    var prenom = p.prenom || "";
    var telephone = p.telephone || "";
    var ville = p.ville || "";
    var eglise = p.eglise || "";
    var participation = p.participation || "";
    var message = p.message || "";
    var groupe_whatsapp = p.groupe_whatsapp || "";

    // 3. Ajout de la ligne dans le Google Sheet
    sheet.appendRow([
      dateStr,
      nom,
      prenom,
      telephone,
      ville,
      eglise,
      participation,
      message,
      groupe_whatsapp
    ]);

    // 4. (Optionnel) Notification par Email
    var emailDestinataire = "VOTRE_EMAIL@GMAIL.COM"; // <-- REMPLACEZ PAR VOTRE ADRESSE EMAIL
    if (emailDestinataire && emailDestinataire !== "VOTRE_EMAIL@GMAIL.COM") {
      var sujet = "🔥 Nouvelle inscription Croisade : " + prenom + " " + nom;
      var corps = "Une nouvelle inscription a été enregistrée sur le site !\n\n" +
                  "• Date : " + dateStr + "\n" +
                  "• Nom & Prénom : " + nom + " " + prenom + "\n" +
                  "• Téléphone : " + telephone + "\n" +
                  "• Ville : " + ville + "\n" +
                  "• Église : " + eglise + "\n" +
                  "• Mode de participation : " + participation + "\n" +
                  "• Attentes : " + message + "\n" +
                  "• Groupe WhatsApp : " + groupe_whatsapp + "\n\n" +
                  "Lien vers votre Google Sheet :\n" +
                  SpreadsheetApp.getActiveSpreadsheet().getUrl();
      MailApp.sendEmail(emailDestinataire, sujet, corps);
    }

    // 5. Réponse de confirmation
    return ContentService
      .createTextOutput(JSON.stringify({ status: "success", message: "Inscription enregistrée !" }))
      .setMimeType(ContentService.MimeType.JSON);

  } catch (error) {
    return ContentService
      .createTextOutput(JSON.stringify({ status: "error", error: error.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}
