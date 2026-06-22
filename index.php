<?php
/**
 * Generador de Schema JSON-LD para SEO
 * Genera datos estructurados Schema.org en formato JSON-LD
 */
header('Content-Type: text/html; charset=utf-8');

$tipo = $_POST['tipo'] ?? 'Organization';
$jsonld = '';
$datos = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? 'Organization';

    switch ($tipo) {
        case 'Organization':
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => trim($_POST['org_name'] ?? ''),
                'url' => trim($_POST['org_url'] ?? ''),
                'logo' => trim($_POST['org_logo'] ?? ''),
                'description' => trim($_POST['org_desc'] ?? ''),
                'email' => trim($_POST['org_email'] ?? ''),
                'telephone' => trim($_POST['org_phone'] ?? ''),
            ];
            if (!empty($_POST['org_social'])) {
                $sociales = array_filter(array_map('trim', explode("\n", $_POST['org_social'])));
                if (!empty($sociales)) $schema['sameAs'] = $sociales;
            }
            break;

        case 'LocalBusiness':
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'LocalBusiness',
                'name' => trim($_POST['lb_name'] ?? ''),
                'url' => trim($_POST['lb_url'] ?? ''),
                'telephone' => trim($_POST['lb_phone'] ?? ''),
                'priceRange' => trim($_POST['lb_price'] ?? ''),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => trim($_POST['lb_street'] ?? ''),
                    'addressLocality' => trim($_POST['lb_city'] ?? ''),
                    'addressRegion' => trim($_POST['lb_region'] ?? ''),
                    'postalCode' => trim($_POST['lb_zip'] ?? ''),
                    'addressCountry' => trim($_POST['lb_country'] ?? ''),
                ],
            ];
            break;

        case 'Article':
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => trim($_POST['art_title'] ?? ''),
                'description' => trim($_POST['art_desc'] ?? ''),
                'image' => trim($_POST['art_img'] ?? ''),
                'datePublished' => trim($_POST['art_date'] ?? ''),
                'dateModified' => trim($_POST['art_modified'] ?? '') ?: trim($_POST['art_date'] ?? ''),
                'author' => [
                    '@type' => 'Person',
                    'name' => trim($_POST['art_author'] ?? ''),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => trim($_POST['art_publisher'] ?? ''),
                ],
            ];
            break;

        case 'Product':
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => trim($_POST['prod_name'] ?? ''),
                'description' => trim($_POST['prod_desc'] ?? ''),
                'image' => trim($_POST['prod_img'] ?? ''),
                'brand' => [
                    '@type' => 'Brand',
                    'name' => trim($_POST['prod_brand'] ?? ''),
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'price' => trim($_POST['prod_price'] ?? ''),
                    'priceCurrency' => trim($_POST['prod_currency'] ?? 'USD'),
                    'availability' => 'https://schema.org/InStock',
                    'url' => trim($_POST['prod_url'] ?? ''),
                ],
            ];
            break;

        case 'FAQPage':
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => [],
            ];
            $preguntas = trim($_POST['faq_data'] ?? '');
            if ($preguntas !== '') {
                $bloques = preg_split('/\n\s*\n/', $preguntas);
                foreach ($bloques as $bloque) {
                    $lineas = array_filter(array_map('trim', explode("\n", trim($bloque))));
                    if (count($lineas) >= 2) {
                        $pregunta = ltrim(array_shift($lineas), 'P: ');
                        $respuesta = ltrim(implode(' ', $lineas), 'R: ');
                        $schema['mainEntity'][] = [
                            '@type' => 'Question',
                            'name' => $pregunta,
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => $respuesta,
                            ],
                        ];
                    }
                }
            }
            break;

        default:
            $schema = ['@context' => 'https://schema.org', '@type' => $tipo];
    }

    // Limpiar valores vacíos recursivamente
    function limpiarVacios($arr) {
        foreach ($arr as $k => &$v) {
            if (is_array($v)) {
                $v = limpiarVacios($v);
                if (empty($v)) unset($arr[$k]);
            } elseif ($v === '' || $v === null) {
                unset($arr[$k]);
            }
        }
        return $arr;
    }

    $schema = limpiarVacios($schema);
    $jsonld = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generador Schema JSON-LD para SEO Online | ConfiguroWeb</title>
<meta name="description" content="Genera datos estructurados Schema.org en formato JSON-LD para mejorar tu SEO. Organization, LocalBusiness, Article, Product y FAQ.">
<meta name="keywords" content="schema json-ld, datos estructurados, seo, schema.org, rich snippets, google">
<meta property="og:type" content="website">
<meta property="og:title" content="Generador Schema JSON-LD para SEO Online">
<meta property="og:description" content="Genera datos estructurados Schema.org en formato JSON-LD para mejorar tu SEO.">
<link rel="canonical" href="https://demoscweb.com/github/php-generador-schema-jsonld/">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Generador Schema JSON-LD","applicationCategory":"UtilitiesApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"},"author":{"@type":"Person","name":"ConfiguroWeb","url":"https://configuroweb.com"}}
</script>
<link rel="stylesheet" href="assets/style.css">
<style>
.campos-tipo{display:none}.campos-tipo.activo{display:block}
.tab-btns{display:flex;flex-wrap:wrap;gap:.4rem;margin:1rem 0}
.tab-btn{flex:1;min-width:100px;padding:.5rem;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface);color:var(--muted);cursor:pointer;font-size:.8rem;text-align:center;transition:all .2s}
.tab-btn.activo{background:var(--primary);color:#fff;border-color:var(--primary)}
</style>
</head>
<body>
<header>
  <h1>📐 Generador Schema JSON-LD</h1>
  <p class="subtitle">Datos estructurados para SEO y Rich Snippets</p>
</header>
<main>
  <form method="POST" id="schemaForm">
    <label>Tipo de Schema</label>
    <div class="tab-btns">
      <?php
      $tipos = ['Organization'=>'🏢 Organización','LocalBusiness'=>'📍 Negocio Local','Article'=>'📰 Artículo','Product'=>'🛍️ Producto','FAQPage'=>'❓ FAQ'];
      foreach ($tipos as $k => $v):
      ?>
      <button type="button" class="tab-btn <?php if($tipo===$k) echo 'activo'; ?>" onclick="setTipo('<?php echo $k; ?>')"><?php echo $v; ?></button>
      <?php endforeach; ?>
    </div>
    <input type="hidden" name="tipo" id="tipoInput" value="<?php echo htmlspecialchars($tipo); ?>">

    <!-- Organization -->
    <div class="campos-tipo <?php if($tipo==='Organization') echo 'activo'; ?>" id="campos-Organization">
      <label for="org_name">Nombre de la organización *</label>
      <input type="text" name="org_name" id="org_name" value="<?php echo htmlspecialchars($_POST['org_name']??''); ?>" placeholder="Mi Empresa S.A.">
      <label for="org_url">URL del sitio web</label>
      <input type="text" name="org_url" id="org_url" value="<?php echo htmlspecialchars($_POST['org_url']??''); ?>" placeholder="https://miempresa.com">
      <label for="org_logo">URL del logo</label>
      <input type="text" name="org_logo" id="org_logo" value="<?php echo htmlspecialchars($_POST['org_logo']??''); ?>" placeholder="https://miempresa.com/logo.png">
      <label for="org_desc">Descripción</label>
      <input type="text" name="org_desc" id="org_desc" value="<?php echo htmlspecialchars($_POST['org_desc']??''); ?>" placeholder="Empresa líder en...">
      <label for="org_email">Email</label>
      <input type="email" name="org_email" id="org_email" value="<?php echo htmlspecialchars($_POST['org_email']??''); ?>" placeholder="info@miempresa.com">
      <label for="org_phone">Teléfono</label>
      <input type="text" name="org_phone" id="org_phone" value="<?php echo htmlspecialchars($_POST['org_phone']??''); ?>" placeholder="+57 300 123 4567">
      <label for="org_social">Redes sociales (una URL por línea)</label>
      <textarea name="org_social" id="org_social" rows="3" placeholder="https://facebook.com/miempresa
https://instagram.com/miempresa
https://x.com/miempresa"><?php echo htmlspecialchars($_POST['org_social']??''); ?></textarea>
    </div>

    <!-- LocalBusiness -->
    <div class="campos-tipo <?php if($tipo==='LocalBusiness') echo 'activo'; ?>" id="campos-LocalBusiness">
      <label for="lb_name">Nombre del negocio *</label>
      <input type="text" name="lb_name" value="<?php echo htmlspecialchars($_POST['lb_name']??''); ?>" placeholder="Restaurante El Buen Sabor">
      <label for="lb_url">URL</label>
      <input type="text" name="lb_url" value="<?php echo htmlspecialchars($_POST['lb_url']??''); ?>" placeholder="https://elbuensabor.com">
      <label for="lb_phone">Teléfono</label>
      <input type="text" name="lb_phone" value="<?php echo htmlspecialchars($_POST['lb_phone']??''); ?>" placeholder="+57 1 234 5678">
      <label for="lb_price">Rango de precios</label>
      <input type="text" name="lb_price" value="<?php echo htmlspecialchars($_POST['lb_price']??''); ?>" placeholder="$$ (ej: $, $$, $$$)">
      <label for="lb_street">Dirección</label>
      <input type="text" name="lb_street" value="<?php echo htmlspecialchars($_POST['lb_street']??''); ?>" placeholder="Calle 100 #15-20">
      <label for="lb_city">Ciudad</label>
      <input type="text" name="lb_city" value="<?php echo htmlspecialchars($_POST['lb_city']??''); ?>" placeholder="Bogotá">
      <label for="lb_region">Región/Estado</label>
      <input type="text" name="lb_region" value="<?php echo htmlspecialchars($_POST['lb_region']??''); ?>" placeholder="Cundinamarca">
      <label for="lb_zip">Código postal</label>
      <input type="text" name="lb_zip" value="<?php echo htmlspecialchars($_POST['lb_zip']??''); ?>" placeholder="110111">
      <label for="lb_country">País (código ISO)</label>
      <input type="text" name="lb_country" value="<?php echo htmlspecialchars($_POST['lb_country']??'CO'); ?>" placeholder="CO">
    </div>

    <!-- Article -->
    <div class="campos-tipo <?php if($tipo==='Article') echo 'activo'; ?>" id="campos-Article">
      <label for="art_title">Título del artículo *</label>
      <input type="text" name="art_title" value="<?php echo htmlspecialchars($_POST['art_title']??''); ?>" placeholder="Guía completa de SEO 2026">
      <label for="art_desc">Descripción</label>
      <input type="text" name="art_desc" value="<?php echo htmlspecialchars($_POST['art_desc']??''); ?>" placeholder="Aprende todo sobre SEO...">
      <label for="art_img">URL de imagen principal</label>
      <input type="text" name="art_img" value="<?php echo htmlspecialchars($_POST['art_img']??''); ?>" placeholder="https://midominio.com/imagen.jpg">
      <label for="art_author">Autor</label>
      <input type="text" name="art_author" value="<?php echo htmlspecialchars($_POST['art_author']??''); ?>" placeholder="Juan Pérez">
      <label for="art_publisher">Editorial/Publicador</label>
      <input type="text" name="art_publisher" value="<?php echo htmlspecialchars($_POST['art_publisher']??''); ?>" placeholder="Mi Blog">
      <label for="art_date">Fecha de publicación</label>
      <input type="date" name="art_date" value="<?php echo htmlspecialchars($_POST['art_date']??date('Y-m-d')); ?>">
      <label for="art_modified">Fecha de última modificación</label>
      <input type="date" name="art_modified" value="<?php echo htmlspecialchars($_POST['art_modified']??''); ?>">
    </div>

    <!-- Product -->
    <div class="campos-tipo <?php if($tipo==='Product') echo 'activo'; ?>" id="campos-Product">
      <label for="prod_name">Nombre del producto *</label>
      <input type="text" name="prod_name" value="<?php echo htmlspecialchars($_POST['prod_name']??''); ?>" placeholder="Camiseta de algodón premium">
      <label for="prod_desc">Descripción</label>
      <input type="text" name="prod_desc" value="<?php echo htmlspecialchars($_POST['prod_desc']??''); ?>" placeholder="Camiseta 100% algodón...">
      <label for="prod_img">URL de imagen</label>
      <input type="text" name="prod_img" value="<?php echo htmlspecialchars($_POST['prod_img']??''); ?>" placeholder="https://mitienda.com/camiseta.jpg">
      <label for="prod_brand">Marca</label>
      <input type="text" name="prod_brand" value="<?php echo htmlspecialchars($_POST['prod_brand']??''); ?>" placeholder="Mi Marca">
      <label for="prod_price">Precio</label>
      <input type="number" step="0.01" name="prod_price" value="<?php echo htmlspecialchars($_POST['prod_price']??''); ?>" placeholder="49.99">
      <label for="prod_currency">Moneda (ISO)</label>
      <input type="text" name="prod_currency" value="<?php echo htmlspecialchars($_POST['prod_currency']??'USD'); ?>" placeholder="USD, COP, EUR, MXN">
      <label for="prod_url">URL del producto</label>
      <input type="text" name="prod_url" value="<?php echo htmlspecialchars($_POST['prod_url']??''); ?>" placeholder="https://mitienda.com/camiseta">
    </div>

    <!-- FAQPage -->
    <div class="campos-tipo <?php if($tipo==='FAQPage') echo 'activo'; ?>" id="campos-FAQPage">
      <label for="faq_data">Preguntas y respuestas</label>
      <textarea name="faq_data" id="faq_data" rows="10" placeholder="¿Cuánto cuesta el servicio?
Nuestro servicio cuesta $99/mes con todas las funciones incluidas.

¿Ofrecen prueba gratuita?
Sí, ofrecemos 14 días de prueba gratuita sin tarjeta de crédito."><?php echo htmlspecialchars($_POST['faq_data']??''); ?></textarea>
      <p style="color:var(--muted);font-size:.8rem;margin-top:.3rem">Escribe la pregunta en una línea, la respuesta en la siguiente. Separa cada par con una línea en blanco.</p>
    </div>

    <button type="submit" class="btn-primary">📐 Generar JSON-LD</button>
  </form>

  <?php if ($jsonld !== ''): ?>
  <div class="resultados" style="margin-top:1.5rem">
    <h2 style="margin-bottom:.5rem;font-size:1.1rem">Schema JSON-LD generado</h2>
    <p style="color:var(--muted);font-size:.8rem;margin-bottom:.5rem">Pega este código antes de <code style="color:#93c5fd">&lt;/head&gt;</code> en tu HTML:</p>
    <pre style="background:#0f172a;padding:1rem;border-radius:var(--radius);font-family:'Cascadia Code',Consolas,monospace;font-size:.8rem;color:#93c5fd;overflow-x:auto;white-space:pre;line-height:1.4;max-height:400px;overflow-y:auto"><code>&lt;script type="application/ld+json"&gt;
<?php echo htmlspecialchars($jsonld); ?>

&lt;/script&gt;</code></pre>
  </div>
  <?php endif; ?>

  <section class="info">
    <h2>¿Qué es Schema JSON-LD?</h2>
    <p><strong>JSON-LD</strong> es el formato recomendado por Google para implementar datos estructurados de <strong>Schema.org</strong> en tu sitio web.</p>
    <p>Los datos estructurados ayudan a Google a entender tu contenido y pueden generar <strong>rich snippets</strong> (resultados enriquecidos) en los resultados de búsqueda.</p>
    <p><strong>Valida tu código:</strong> Usa la herramienta de <a href="https://search.google.com/test/rich-results" target="_blank" style="color:var(--primary)">prueba de resultados enriquecidos de Google</a>.</p>
  </section>
</main>
<footer>
  <p>Desarrollado por <a href="https://configuroweb.com" target="_blank">ConfiguroWeb</a> ·
     <a href="https://appscweb.com/citas/" target="_blank">Sistema de Citas</a> ·
     <a href="https://appscweb.com/negocios/" target="_blank">Gestión de Negocios</a></p>
  <p>&copy; <?php echo date('Y'); ?> ConfiguroWeb</p>
</footer>
<script>
function setTipo(t) {
  document.getElementById('tipoInput').value = t;
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('activo'));
  document.querySelectorAll('.campos-tipo').forEach(c => c.classList.remove('activo'));
  event.target.classList.add('activo');
  var el = document.getElementById('campos-' + t);
  if (el) el.classList.add('activo');
}
</script>
</body>
</html>
