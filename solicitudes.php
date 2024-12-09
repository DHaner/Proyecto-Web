<?php
session_start();
include("conexion.php");

// Consulta para obtener libros, estados de solicitudes y datos del usuario solicitante
$Query = "SELECT libro.*, solicitudes.estado, solicitudes.usuario_sol AS solicitante, usuarios.telefono AS telefono 
          FROM libro 
          INNER JOIN solicitudes 
          ON libro.nombre = solicitudes.libro 
          INNER JOIN usuarios 
          ON solicitudes.usuario_sol = usuarios.user
          WHERE solicitudes.usuario_pres = :username";
$Result = $conn->prepare($Query);
$Result->execute([':username' => $_SESSION["username"]]);

// Procesar solicitudes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $libro = $_POST['libro'];

    if ($action == 'accept') {
        $updateQuery = "UPDATE solicitudes SET estado = 'aceptado' WHERE libro = :libro AND usuario_pres = :usuario";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([':libro' => $libro, ':usuario' => $_SESSION["username"]]);
        echo "Solicitud aceptada.";
    } elseif ($action == 'reject') {
        $updateQuery = "UPDATE solicitudes SET estado = 'rechazado' WHERE libro = :libro AND usuario_pres = :usuario";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([':libro' => $libro, ':usuario' => $_SESSION["username"]]);
        echo "Solicitud rechazada.";
    } elseif ($action == 'finalize') {
        $updateQuery = "UPDATE solicitudes SET estado = 'finalizado' WHERE libro = :libro AND usuario_pres = :usuario";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([':libro' => $libro, ':usuario' => $_SESSION["username"]]);
        echo "Intercambio finalizado.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/solicitudes.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header>
        <h1><?php echo htmlspecialchars($_SESSION["username"]); ?> 👋</h1>
        <nav>
            <ul>
                <li><a href="home.php">Inicio</a></li>
                <li><a href="solicitudes.php" style="color: #ff5252;">Solicitudes</a></li>
                <li><a href="guardados.php">Guardados</a></li>
                <li><a href="mibiblioteca.php">Biblioteca</a></li>
            </ul>
        </nav>
    </header>

    <h1>Tus Solicitudes</h1>

    <div class="filter-buttons">
        <button onclick="filterByStatus('todos')">Ver Todos</button>
        <button onclick="filterByStatus('aceptado')">Aceptados</button>
        <button onclick="filterByStatus('rechazado')">Rechazados</button>
        <button onclick="filterByStatus('en espera')">En espera</button>
        <button onclick="filterByStatus('finalizado')">Finalizados</button>
    </div>

    <div class="carousel-container">
        <button class="arrow left" onclick="moveCarousel(-1)">&#10094;</button>
        <div class="carousel">
            <?php while ($row = $Result->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="card-container" data-estado="<?php echo strtolower($row['estado']); ?>">
                    <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($row['img']); ?>');"></div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo $row["nombre"]; ?></h3>
                        <div class="card-author"><?php echo $row["autor"]; ?></div>
                        <div class="card-status">Estado: <?php echo ucfirst($row["estado"]); ?></div>

                        <?php if (strtolower($row['estado']) === 'en espera') { ?>
                            <button onclick="acceptRequest('<?php echo $row['nombre']; ?>')">Aceptar</button>
                            <button onclick="rejectRequest('<?php echo $row['nombre']; ?>')">Rechazar</button>
                        <?php } elseif (strtolower($row['estado']) === 'aceptado') { ?>
                            <button onclick="contactUser('<?php echo $row['solicitante']; ?>', '<?php echo $row['telefono']; ?>')">Contacto</button>
                            <button onclick="finalizeRequest('<?php echo $row['nombre']; ?>')">Confirmar intercambio</button>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <button class="arrow right" onclick="moveCarousel(1)">&#10095;</button>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <script>
        function filterByStatus(status) {
            const cards = document.querySelectorAll('.card-container');
            cards.forEach(card => {
                card.style.display = (status === 'todos' || card.dataset.estado === status) ? 'block' : 'none';
            });
        }

        function moveCarousel(direction) {
            const carousel = document.querySelector('.carousel');
            const scrollAmount = 300;
            carousel.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }

        function openModal(content) {
            const modal = document.getElementById('modal');
            const modalBody = document.getElementById('modal-body');
            modalBody.innerHTML = content;
            modal.style.display = 'block';
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            modal.style.display = 'none';
        }

        window.onclick = function (event) {
            const modal = document.getElementById('modal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };

        function acceptRequest(libro) {
            openModal(`
                <h2>¿Aceptar solicitud?</h2>
                <button onclick="confirmAction('accept', '${libro}')">Aceptar</button>
                <button onclick="closeModal()">Cancelar</button>
            `);
        }

        function rejectRequest(libro) {
            openModal(`
                <h2>¿Rechazar solicitud?</h2>
                <button onclick="confirmAction('reject', '${libro}')">Rechazar</button>
                <button onclick="closeModal()">Cancelar</button>
            `);
        }

        function contactUser(usuario, telefono) {
            openModal(`
                <h2>Contacto</h2>
                <p><strong>Usuario:</strong> ${usuario}</p>
                <p><strong>Teléfono:</strong> ${telefono}</p>
                <button onclick="closeModal()">Cerrar</button>
            `);
        }

        function finalizeRequest(libro) {
            openModal(`
                <h2>¿Confirmar intercambio?</h2>
                <button onclick="confirmAction('finalize', '${libro}')">Confirmar</button>
                <button onclick="closeModal()">Cancelar</button>
            `);
        }

        function confirmAction(action, libro) {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=${action}&libro=${encodeURIComponent(libro)}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
        }
    </script>
</body>

</html>
