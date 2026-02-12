<?php
require_once 'config/database.php';

if (ob_get_level()) ob_end_clean();

$query = $pdo->query("SELECT prenom, nom, email, telephone, date_creation FROM contacts ORDER BY nom ASC");
$contacts = $query->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=export_contacts_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['Prénom', 'Nom', 'Email', 'Téléphone', 'Date d\'ajout']);

foreach ($contacts as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit();