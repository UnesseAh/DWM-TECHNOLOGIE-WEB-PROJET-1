<?php
require_once '../config/database.php';

$q = isset($_GET['q']) ? $_GET['q'] : '';

$sql = "SELECT * FROM contacts 
        WHERE nom LIKE :q 
        OR prenom LIKE :q 
        OR email LIKE :q 
        OR telephone LIKE :q 
        ORDER BY nom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['q' => "%$q%"]);
$contacts = $stmt->fetchAll();

if (count($contacts) > 0) {
    foreach ($contacts as $contact) {
        echo "<tr>
                <td><strong>" . htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']) . "</strong></td>
                <td>" . htmlspecialchars($contact['email']) . "</td>
                <td>" . htmlspecialchars($contact['telephone']) . "</td>
                <td>" . date('d/m/Y', strtotime($contact['date_creation'])) . "</td>
                <td>
                    <a href='modifier.php?id={$contact['id']}' class='btn btn-sm btn-outline-info'><i class='fas fa-edit'></i></a>
                    <a href='actions/supprimer.php?id={$contact['id']}' class='btn btn-sm btn-outline-danger' onclick=\"return confirm('Are you sure?')\"><i class='fas fa-trash'></i></a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center text-muted'>No results found for '$q'</td></tr>";
}