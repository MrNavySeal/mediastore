<?php
    headerPage($data);
    $company = getCompanyInfo();
    $social = getSocialMedia();

    $links ="";
    for ($i=0; $i < count($social) ; $i++) { 
        if($social[$i]['link']!=""){
            if($social[$i]['name']=="whatsapp"){
                $links.='<a href="https://wa.me/'.$social[$i]['link'].'" target="_blank" class="me-3 ms-3 text-dark fs-5"><i class="fab fa-'.$social[$i]['name'].'"></i></a>';
            }else{
                $links.='<a href="'.$social[$i]['link'].'" target="_blank" class="me-3 ms-3 text-dark fs-5"><i class="fab fa-'.$social[$i]['name'].'"></i></a>';
            }
        }
    }
?>
    <main id="<?=$data['page_name']?>">
        <div class="cover">
            <h1>CONTACTO</h1>
        </div>
        <div class="container mt-4 mb-4">
            <nav class="mt-2 mb-2" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>">Inicio</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Contacto</li>
                </ol>
            </nav>
            <div class="row contact">
                <div class="col-md-6">
                    <div class="row h-100">
                        <div class="col-md-6 contact-info">
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt fs-1 mb-3 t-p"></i>
                                <p class="fs-6 fw-bold m-0">Nuestra dirección</p>
                                <p class="fs-6"><?=$company['addressfull']?></p>
                            </div>
                        </div>
                        <div class="col-md-6 contact-info">
                            <div class="contact-item">
                                <i class="fas fa-phone fs-1 mb-3 t-p"></i>
                                <p class="fs-6 fw-bold m-0">Nuestro teléfono</p>
                                <p class="fs-6">+<?=$company['phonecode'].' '.$company['phone']?></p>
                            </div>
                        </div>
                        <div class="col-md-6 contact-info">
                            <div class="contact-item">
                                <i class="fas fa-envelope fs-1 mb-3 t-p"></i>
                                <p class="fs-6 fw-bold m-0">Nuestro email</p>
                                <p class="fs-6"><?=$company['email']?></p>
                            </div>
                        </div>
                        <div class="col-md-6 contact-info">
                            <div class="contact-item">
                                <img src="<?=media()."/images/uploads/".$company['logo']?>" alt="<?=$company['name']?>" width="100px" height="100px">
                                <p class="fs-6 fw-bold"><?=$company['name']?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 contact-form">
                    <form id="formContact">
                        <div class="mb-3">
                            <label for="txtContactName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="txtContactName" name="txtContactName" placeholder="Tu nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="txtContactEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="txtContactEmail" name="txtContactEmail" placeholder="Tu correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="txtSubject" class="form-label">Asunto</label>
                            <input type="text" class="form-control" id="txtSubject" name="txtSubject" placeholder="Asunto">
                        </div>
                        <div class="mb-3">
                            <label for="txtMessageEmail" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="txtContactMessage" name="txtContactMessage" placeholder="Tu mensaje" rows="5" required></textarea>
                        </div>
                        <div class="alert alert-danger d-none" id="alertContact" role="alert"></div>
                        <div>
                            <button type="submit" id="btnMessage" class="btn btnc-primary w-100 mb-2">Enviar</button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
<?php
    footerPage($data);
?>