
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<style>
    .retorno-consulta {
        text-align: -webkit-center;
    }
    .width100 {
        width: 100%;
    }
    .hr-style {
        margin: 0px 75px;
    }
</style>
<body>
<form class="js-ajax-php-json" method="post" accept-charset="utf-8">
    <div class="form-group mx-sm-3 input-group">
        <label class="sr-only">
            <input class="form-control nome-dominio width100" type="text" name="domain" value="" placeholder="Domínio"/>
        </label>
        <span class="input-group-btn">
            <button type="submit" name="submit" class="btn btn-secondary btn-consulta">Consultar</button>
        </span>
    </div>

</form>
<div class="retorno-consulta"></div>
</body>
<footer>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            let regex = /(.br)/g;
            jQuery(".btn-consulta").attr("disabled", "true");
            jQuery('.nome-dominio').keyup(function () {
                let val = jQuery(this).val();
                if ( regex.test(val) ) {
                    jQuery(".btn-consulta").removeAttr("disabled");
                } else {
                    jQuery(".btn-consulta").attr("disabled", "true");
                }
            });
        });
    </script>

    <script type="text/javascript">
        jQuery("document").ready(function () {
            jQuery(".js-ajax-php-json").submit(function () {
                let ajaxscript = '<?php echo admin_url('admin-ajax.php'); ?>';
                let data = {"action": "registro_br"};

                data = jQuery(this).serialize() + "&" + jQuery.param(data);
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ajaxscript, //Relative or absolute path to pesquisa-dominio.php file
                    data: data ,
                    success: function (data) {
                        const statusArr = [
                            "Disponível", // Status 0
                            "Disponivel com ticket ativo", // Status 1
                            "Registrado", // Status 2
                            "Indisponivel", // Status 3
                            "Domínio Inválido", // Status 4
                            "Aguardando processo de liberacao", // Status 5
                            "Processo de liberacao em progresso", // Status 6
                            "Processo de liberacao em progresso", // Status 7
                            "Erro", // Status 8
                            "Processo de liberacao competitivo em andamento com ticket ativo"]; // Status 10
                        const alertStatus = statusArr.at(data["status"]);
                        let alert;
                        switch (alertStatus) {
                            case 'Disponível':
                                alert = 'alert-success';
                                break;
                            case 'Registrado':
                                alert = 'alert-warning';
                                break;
                            case 'Erro':
                                alert = 'alert-danger';
                                break;
                            case 'Domínio Inválido':
                                alert = 'alert-danger';
                                break;
                            default:
                                alert = 'alert-dark';
                        }
                        jQuery(".retorno-consulta").html(
                            "<div class='alert " + alert + " alert-dismissible fade show' role='alert'> " +
                            "   <h4 class='alert-heading'>" + data["domain"] + " </h4> " +
                            "   <p>Status do Domínio: " + statusArr.at(data["status"]) + "</p>" +
                            "   <hr class='hr-style'>" +
                            "</div> "
                        );
                    }
                });
                return false;
            });
        });
    </script>
</footer>

