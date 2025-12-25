<?php
require "includes/connection.php";
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== "yes") {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <title>Kreiraj Novi Kviz | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .form-card { max-width: 600px; margin: 50px auto; border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <div class="card form-card">
        <div class="card-header bg-success text-white py-3">
            <h4 class="mb-0"><i class="fa fa-plus-circle me-2"></i> Kreiraj Novi Kviz</h4>
        </div>
        <div class="card-body p-4">
            <form action="spasiKviz.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold">Naziv kviza</label>
                    <input type="text" name="naziv_kviza" class="form-control" placeholder="npr. Geografija Svijeta" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Naslovna slika kviza</label>
                    <input type="file" name="kviz_slika" class="form-control" accept="image/*">
                    <div class="form-text">Preporučeno: pravougaona slika (npr. 800x400px).</div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Vrijeme po pitanju (sek)</label>
                        <input type="number" name="vrijeme" class="form-control" value="15" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Broj pitanja</label>
                        <input type="number" name="broj_pitanja" class="form-control" placeholder="npr. 5" required>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-success btn-lg">Dalje: Unesi Pitanja</button>
                    <a href="admin.php" class="btn btn-light">Odustani</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>