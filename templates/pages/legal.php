<?php
$pageTitle = 'Mentions légales et confidentialité';
$needsSwiper = false;
partial('breadcrumb', ['title' => 'Mentions légales', 'crumbs' => [['Mentions légales', null]]]);
?>
<section class="section">
    <div class="container">
        <div class="legal-content prose">
            <h2 id="editeur">Éditeur du site</h2>
            <p><strong><?= e(setting('company_name', 'GELPAZ IMMO SA')) ?></strong> — société de promotion et de vente immobilière.<br>
            Siège : <?= e(setting('address')) ?><br>
            Téléphone : <?= e(setting('phone')) ?> · WhatsApp : <?= e(setting('phone_mobile')) ?><br>
            E-mail : <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
            <h2 id="hebergement">Hébergement</h2>
            <p>Les informations relatives à l’hébergeur du site sont disponibles sur simple demande auprès de GELPAZ IMMO.</p>
            <h2 id="propriete">Propriété intellectuelle</h2>
            <p>L’ensemble des contenus de ce site (textes, logos, photographies, visuels) est la propriété de GELPAZ IMMO ou de ses partenaires. Toute reproduction sans autorisation préalable est interdite. Certains visuels d’illustration (bannières, ambiances) sont des créations numériques et ne représentent pas des biens précis.</p>
            <h2 id="prix">Informations commerciales</h2>
            <p>Les descriptions des logements sont fournies à titre indicatif et peuvent évoluer. Les prix sont communiqués sur demande et ne constituent pas une offre contractuelle tant qu’ils n’ont pas été confirmés par écrit par GELPAZ IMMO.</p>
            <h2 id="confidentialite">Protection des données personnelles</h2>
            <p>Les informations que vous transmettez via nos formulaires (nom, coordonnées, message) sont utilisées uniquement pour répondre à votre demande et assurer le suivi commercial de votre projet. Elles ne sont ni vendues ni cédées à des tiers.</p>
            <ul>
                <li>Responsable du traitement : GELPAZ IMMO SA ;</li>
                <li>Durée de conservation : le temps nécessaire au traitement de votre demande et au suivi de la relation commerciale ;</li>
                <li>Vos droits : vous pouvez demander l’accès, la rectification ou la suppression de vos données en écrivant à <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a>.</li>
            </ul>
            <h2 id="cookies">Cookies et services tiers</h2>
            <p>Le site utilise un cookie de session technique (sécurité des formulaires) et mémorise vos favoris dans votre navigateur. Si vous choisissez l’anglais ou l’italien, le service de traduction automatique de Google est chargé. Les cartes sont fournies par Google Maps et la vidéo de présentation par YouTube (mode confidentialité renforcée).</p>
        </div>
    </div>
</section>
