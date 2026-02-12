<?php 
require_once 'config/database.php';

$query = $pdo->query("SELECT * FROM contacts ORDER BY date_creation DESC");
$contacts = $query->fetchAll();

include 'includes/header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Contact List</h2>
    <a href="ajouter.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Added On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($contacts) > 0): ?>
                <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                        <td><?php echo htmlspecialchars($contact['telephone']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($contact['date_creation'])); ?></td>
                        <td>
                            <a href="modifier.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="actions/supprimer.php?id=<?php echo $contact['id']; ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Are you sure you want to delete this contact?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No contacts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>