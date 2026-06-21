<?php
header('Content-Type: text/html; charset=utf-8');
$input = $_POST['input'] ?? '';
$resultado = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input !== '') {
    $resultado = $input;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generador Schema JSON-LD Online | ConfiguroWeb</title>
<meta name="description" content="Generador Schema JSON-LD online gratis. Herramienta web de ConfiguroWeb.">
<meta name="keywords" content="Generador Schema JSON-LD, herramienta online, gratis, configuroweb">
<meta property="og:type" content="website">
<meta property="og:title" content="Generador Schema JSON-LD Online">
<meta property="og:description" content="Generador Schema JSON-LD online gratis.">
<link rel="canonical" href="https://demoscweb.com/github/php-generador-schema-jsonld/">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Generador Schema JSON-LD","applicationCategory":"UtilitiesApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"},"author":{"@type":"Person","name":"ConfiguroWeb","url":"https://configuroweb.com"}}
</script>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
  <h1>📐 Generador Schema JSON-LD</h1>
  <p class="subtitle">Herramienta online gratis</p>
</header>
<main>
  <form method="POST">
    <label for="input">Datos de entrada:</label>
    <textarea name="input" id="input" rows="6" placeholder="Ingresa aqui tus datos..."></textarea>
    <button type="submit" class="btn-primary">Procesar</button>
  </form>

  <?php if ($resultado !== ''): ?>
  <div class="resultado">
    <span class="etiqueta">Resultado</span>
    <div class="valor" style="font-size:1rem;padding:1rem;text-align:left;white-space:pre-wrap"><?php echo htmlspecialchars($resultado); ?></div>
  </div>
  <?php endif; ?>

  <section class="info">
    <h2>Acerca de Generador Schema JSON-LD</h2>
    <p>Esta herramienta de <strong>Generador Schema JSON-LD</strong> esta en desarrollo activo. Pronto con mas funciones avanzadas.</p>
  </section>
</main>
<footer>
  <p>Desarrollado por <a href="https://configuroweb.com" target="_blank">ConfiguroWeb</a> ·
     <a href="https://appscweb.com/citas/" target="_blank">Sistema de Citas</a> ·
     <a href="https://appscweb.com/negocios/" target="_blank">Gestion de Negocios</a></p>
  <p>&copy; 2026 ConfiguroWeb</p>
</footer>
<script src="assets/script.js"></script>
</body>
</html>
