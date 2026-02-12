<?php
require_once 'config/database.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];

    if (!empty($nom) && !empty($prenom) && !empty($email)) {
        
        $check = $pdo->prepare("SELECT id FROM contacts WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            $message = "<div class='alert alert-danger'>Error: This email is already in your contacts.</div>";
        } else {
            $sql = "INSERT INTO contacts (nom, prenom, email, telephone, adresse) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$nom, $prenom, $email, $telephone, $adresse])) {
                $message = "<div class='alert alert-success'>Contact added successfully! <a href='index.php'>View list</a></div>";
            }
        }
    } else {
        $message = "<div class='alert alert-warning'>Please fill in all required fields.</div>";
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Add New Contact</h4>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                
                <form action="ajouter.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name *</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="telephone" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="adresse" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Save Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>