<?php
require_once 'config/database.php';

$message = "";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$id]);
$contact = $stmt->fetch();

if (!$contact) {
    die("Contact not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];

    if (!empty($nom) && !empty($prenom) && !empty($email)) {
        
        $check = $pdo->prepare("SELECT id FROM contacts WHERE email = ? AND id != ?");
        $check->execute([$email, $id]);
        
        if ($check->rowCount() > 0) {
            $message = "<div class='alert alert-danger'>Error: This email is already used by another contact.</div>";
        } else {
            $sql = "UPDATE contacts SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ? WHERE id = ?";
            $updateStmt = $pdo->prepare($sql);
            
            if ($updateStmt->execute([$nom, $prenom, $email, $telephone, $adresse, $id])) {
                $message = "<div class='alert alert-success'>Contact updated successfully! <a href='index.php'>Back to list</a></div>";
                // Refresh data to show updated values in form
                $stmt->execute([$id]);
                $contact = $stmt->fetch();
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-info">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Edit Contact: <?php echo htmlspecialchars($contact['prenom']); ?></h4>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                
                <form action="modifier.php?id=<?php echo $id; ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($contact['prenom']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($contact['nom']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($contact['email']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($contact['telephone']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="adresse" class="form-control" rows="3"><?php echo htmlspecialchars($contact['adresse']); ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-info text-white">Update Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>