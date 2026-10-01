<?php
/**
 * Rapatrie sur votre hébergement toutes les images de l'ancien site (gelpaz.com) :
 *   php tools/import-media.php
 * Les images sont redimensionnées et enregistrées dans uploads/, puis la base est mise à jour.
 */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit("CLI uniquement.\n");
}
require __DIR__ . '/../app/bootstrap.php';
if (!is_installed()) {
    exit("Le site n'est pas encore installé.\n");
}
$skip = [];
$imported = 0;
do {
    $r = App\Media::importBatch(5, $skip);
    $imported += $r['done'];
    foreach ($r['errors'] as $err) {
        $skip[] = $err['key'];
        fwrite(STDERR, "  ✗ {$err['url']} — {$err['error']}\n");
    }
    echo "  ✓ {$imported} image(s) importée(s), {$r['remaining']} restante(s)\n";
} while ($r['remaining'] > 0 && ($r['done'] > 0 || $r['errors']));
echo "Terminé : {$imported} image(s) importée(s), " . count($skip) . " échec(s).\n";
