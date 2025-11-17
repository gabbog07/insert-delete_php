<?php
$servername = 'db';
$username = 'myuser';
$password = 'mypassword';
$database = 'myapp_db';

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) die("Errore connessione: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $id = intval(isset($_POST['id']) ? $_POST['id'] : 0);
    $azione = isset($_POST['azione']) ? $_POST['azione'] : '';

    if ($nome && $email) {
        $stmt = $conn->prepare("INSERT INTO utenti (nome, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $email);
        $msg = $stmt->execute() ? "Utente aggiunto!" : "Errore: " . $stmt->error;
    } elseif ($azione === 'elimina' && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM utenti WHERE id = ?");
        $stmt->bind_param("i", $id);
        $msg = $stmt->execute() ? "Utente eliminato!" : "Errore: " . $stmt->error;
    } else {
        $msg = "Compila tutti i campi!";
    }
}

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

    <?php if (isset($msg)) : ?>
        <p class="alert alert-info text-center"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <div class="card p-4 mb-4 shadow">
        <form method="POST" class="row g-2">
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

    <div class="card p-4 shadow">
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr><th>ID</th><th>Nome</th><th>Email</th><th>Azione</th></tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) : ?>
                    <?php while ($r = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['nome']) ?></td>
                            <td><?= htmlspecialchars($r['email']) ?></td>
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