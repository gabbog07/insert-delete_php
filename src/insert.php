<?php
// Connessione al DB
$conn = new mysqli('db', 'myuser', 'mypassword', 'myapp_db');
if ($conn->connect_error) die("Errore connessione: " . $conn->connect_error);

$msg = "";

// --- OPERAZIONI POST ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $azione = $_POST['azione'] ?? '';

    // Aggiungi utente
    if ($azione === 'aggiungi') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($nome && $email) {
            $sql = "INSERT INTO utenti (nome, email) VALUES ('$nome', '$email')";
            $msg = $conn->query($sql) ? "Utente aggiunto!" : "Errore: " . $conn->error;
        } else {
            $msg = "Compila nome ed email!";
        }
    }

    // Elimina utente
    if ($azione === 'elimina') {
        $id = $_POST['id'] ?? '';
        if ($id !== '') {
            $sql = "DELETE FROM utenti WHERE id = '$id'";
            $msg = $conn->query($sql) ? "Utente eliminato!" : "Errore: " . $conn->error;
        }
    }
}

// --- CARICA UTENTI ---
$result = $conn->query("SELECT id, nome, email FROM utenti ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Utenti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container">
    <h2 class="text-center mb-4">Gestione Utenti</h2>

    <?php if ($msg) : ?>
        <p class="alert alert-info text-center"><?= $msg ?></p>
    <?php endif; ?>

    <!-- Form aggiunta utente -->
    <div class="card p-4 mb-4 shadow">
        <form method="POST" class="row g-2">
            <input type="hidden" name="azione" value="aggiungi">
            <div class="col-md-5">
                <input type="text" name="nome" placeholder="Nome" class="form-control" required>
            </div>
            <div class="col-md-5">
                <input type="email" name="email" placeholder="Email" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Aggiungi</button>
            </div>
        </form>
    </div>

    <!-- Tabella utenti -->
    <div class="card p-4 shadow">
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr><th>ID</th><th>Nome</th><th>Email</th><th>Azione</th></tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0) : ?>
                    <?php while ($r = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= $r['nome'] ?></td>
                            <td><?= $r['email'] ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="azione" value="elimina">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button class="btn btn-danger btn-sm">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr><td colspan="4">Nessun utente</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
