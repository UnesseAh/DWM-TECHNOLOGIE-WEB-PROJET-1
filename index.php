<?php 
require_once 'config/database.php';

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

$total_stmt = $pdo->query("SELECT COUNT(*) FROM contacts");
$total_contacts = $total_stmt->fetchColumn();
$total_pages = ceil($total_contacts / $limit);

$query = $pdo->prepare("SELECT * FROM contacts ORDER BY nom ASC LIMIT :limit OFFSET :offset");
$query->bindValue(':limit', $limit, PDO::PARAM_INT);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$contacts = $query->fetchAll();

include 'includes/header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Contact List</h2>
    <a href="ajouter.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
</div>

<div class="table-container">
    <div class="mb-3">
    <div class="input-group">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email or phone...">
    </div>
</div>
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
    <?php if ($total_pages > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let query = this.value;
    
    fetch('actions/rechercher.php?q=' + query)
        .then(response => response.text())
        .then(data => {
            document.querySelector('tbody').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
});
</script>

<?php include 'includes/footer.php'; ?>

