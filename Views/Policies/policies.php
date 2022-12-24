<?php
    headerPage($data);
?>
    <main id="<?=$data['page_name']?>">
        <div class="cover">
            <h1><?=$data['page']['name']?></h1>
            
        </div>
        <div class="container">
            <nav class="mt-2 mb-2" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Politicas</li>
                </ol>
            </nav>
        </div>
        <div class="container mb-3 mt-3">
            <?=$data['page']['description']?>
        </div>
    </main>
<?php
    footerPage($data);
?>