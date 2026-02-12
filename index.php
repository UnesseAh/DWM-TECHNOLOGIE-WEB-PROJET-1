<?php 
require_once 'config/database.php';

$limit = 10; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$allowed_columns = ['nom', 'prenom', 'date_creation'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_columns) ? $_GET['sort'] : 'date_creation';
$order = isset($_GET['order']) && strtolower($_GET['order']) == 'asc' ? 'ASC' : 'DESC';

$total_stmt = $pdo->query("SELECT COUNT(*) FROM contacts");
$total_contacts = $total_stmt->fetchColumn();
$total_pages = ceil($total_contacts / $limit);

$query = $pdo->prepare("SELECT * FROM contacts ORDER BY $sort $order LIMIT :limit OFFSET :offset");
$query->bindValue(':limit', $limit, PDO::PARAM_INT);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$contacts = $query->fetchAll();

include 'includes/header.php'; 
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users"></i> Contact List</h2>
        <div>
            <a href="exporter.php" class="btn btn-success me-2">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <a href="ajouter.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New Contact
            </a>
        </div>
    </div>

    <div class="mb-4">
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" 
                   placeholder="Search by name, email, or phone number...">
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="?sort=prenom&order=<?php echo ($sort == 'prenom' && $order == 'ASC') ? 'desc' : 'asc'; ?>" class="text-decoration-none text-dark">
                                First Name <?php if($sort == 'prenom') echo $order == 'ASC' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=nom&order=<?php echo ($sort == 'nom' && $order == 'ASC') ? 'desc' : 'asc'; ?>" class="text-decoration-none text-dark">
                                Last Name <?php if($sort == 'nom') echo $order == 'ASC' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>
                            <a href="?sort=date_creation&order=<?php echo ($sort == 'date_creation' && $order == 'ASC') ? 'desc' : 'asc'; ?>" class="text-decoration-none text-dark">
                                Added Date <?php if($sort == 'date_creation') echo $order == 'ASC' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="contactsTableBody">
                    <?php if (count($contacts) > 0): ?>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contact['prenom']); ?></td>
                                <td><strong><?php echo htmlspecialchars($contact['nom']); ?></strong></td>
                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                <td><?php echo htmlspecialchars($contact['telephone']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($contact['date_creation'])); ?></td>
                                <td class="text-end">
                                    <a href="modifier.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="actions/supprimer.php?id=<?php echo $contact['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete this contact permanentely?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No contacts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="paginationArea" class="mt-4">
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">Previous</a>
                    </li>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const query = this.value;
    const tableBody = document.getElementById('contactsTableBody');
    const pagination = document.getElementById('paginationArea');

    if (query.length === 0) {
        window.location.reload();
        return;
    }

    fetch('./actions/rechercher.php?q=' + encodeURIComponent(query))
        .then(response => response.text())
        .then(data => {
            tableBody.innerHTML = data;
            pagination.style.display = 'none';
        })
        .catch(error => console.error('Error during search:', error));
});
</script>

<?php include 'includes/footer.php'; ?>