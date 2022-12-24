<?php 
    headerAdmin($data);
?>
<div class="body flex-grow-1 px-3" id="<?=$data['page_name']?>">
    <div class="container-lg">
        <div class="card mb-4">
            <div class="card-body">
                <form id="formFile" name="formFile">
                    <div class="row scrolly" id="upload-multiple">
                        <div class="col-6 col-lg-3">
                            <div class="mb-3 upload-images">
                                <label for="txtImg" class="text-primary text-center d-flex justify-content-center align-items-center">
                                    <div>
                                        <i class="far fa-images fs-1"></i>
                                        <p class="m-0">Subir imágen</p>
                                    </div>
                                </label>
                                <input class="d-none" type="file" id="txtImg" name="txtImg[]" multiple accept="image/*"> 
                            </div>
                        </div>
                    </div>
                </form>
                <form id="formItem" name="formItem" class="mb-4">  
                    <input type="hidden" id="idProduct" name="idProduct" value="">
                    <div class="row">
                        <p class="text-center">Todos los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="txtName" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="txtName" name="txtName" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoryList" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select class="form-control" aria-label="Default select example" id="categoryList" name="categoryList" required></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subcategoryList" class="form-label">Subcategoria <span class="text-danger">*</span></label>
                                <select class="form-control" aria-label="Default select example" id="subcategoryList" name="subcategoryList" required></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="txtDiscount" class="form-label">Descuento</label>
                                <input type="number" class="form-control"  max="99" id="txtDiscount" name="txtDiscount">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statusList" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-control" aria-label="Default select example" id="statusList" name="statusList" required>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="txtShortDescription" class="form-label">Descripción corta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txtShortDescription" name="txtShortDescription" placeholder="Max 140 carácteres"></input>
                    </div>
                    <div class="mb-3">
                        <label for="txtDescription" class="form-label">Descripción </label>
                        <textarea class="form-control" id="txtDescription" name="txtDescription" rows="5"></textarea>
                    </div>
                    <hr>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="simple-tab" data-bs-toggle="tab" data-bs-target="#simple-tab-pane" type="button" role="tab" aria-controls="simple-tab-pane" aria-selected="true">Producto sin variantes</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="variant-tab" data-bs-toggle="tab" data-bs-target="#variant-tab-pane" type="button" role="tab" aria-controls="variant-tab-pane" aria-selected="false">Producto con variantes</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="simple-tab-pane" role="tabpanel" aria-labelledby="simple-tab" tabindex="0">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="txtPrice" class="form-label">Precio <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" min ="1" id="txtPrice" name="txtPrice">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="txtStock" class="form-label">Cantidad <span class="text-danger">*</span></label>
                                        <input type="number" value="1" min="0" class="form-control" id="txtStock" name="txtStock">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="variant-tab-pane" role="tabpanel" aria-labelledby="variant-tab" tabindex="0">
                            <p class="mt-3">Agregue variantes si este producto viene en múltiples versiones, como diferentes tamaños o colores.</p>
                            <div id="makeVariant"></div>
                            <button type="button" class="btn btn-primary mt-3 mb-3" id="addVariant">Agregar variante</button>
                            <table class="table items align-middle" id="table<?=$data['page_title']?>">
                                <thead>
                                    <tr class="text-end">
                                        <th>Variante</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody id="listItem"></tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <h3>Características</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="txtWarranty" class="form-label">Garantía</label>
                            <input type="text" class="form-control" id="txtWarranty" name="txtWarranty"></input>
                        </div>
                        <div class="col-md-4">
                            <label for="txtMark" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="txtMark" name="txtMark"></input>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="txtWeight" class="form-label">Peso</label>
                                <input type="number" min="0" class="form-control" id="txtWeight" name="txtWeight" placeholder="Kg">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="txtLength" class="form-label">Largo</label>
                                <input type="number" min="0" class="form-control" id="txtLength" name="txtLength" placeholder="cm">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="txtHeight" class="form-label">Alto</label>
                                <input type="number" min="0" class="form-control" id="txtHeight" name="txtHeight" placeholder="cm">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="txtWidth" class="form-label">Ancho</label>
                                <input type="number" min="0" class="form-control" id="txtWidth" name="txtWidth" placeholder="cm">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2" id="btnAdd"><i class="fas fa-plus-circle"></i> Agregar</button>
                            <a href="<?=BASE_URL?>/inventario/productos" class="btn btn-secondary text-white"> Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php footerAdmin($data)?>     
</script> 
