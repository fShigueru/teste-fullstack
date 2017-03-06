var CONFIG = (function() {
    var private = {
        'ACESS_KEY': "8aea0ad1dc015ac45477f4a9c14ffe03",
        'EMAIL_RETURN': jQuery("#email.return"),
        'EMAIL' : jQuery('#email'),
        'ENDERECO' : jQuery('#endereco'),
        'BUTTON_PESQ' : jQuery('#pesquisar'),
        'ALERT_DANGER' : jQuery(".alert-danger"),
        'MSG_DANGER' : jQuery(".msg-danger"),
        'PATH_SUCCESS' : 'includes/content_success.php',
        'PATH_ROTINA' : 'rotina.php'
    };
    return {
        get: function(name) { return private[name]; }
    };
})();

var valida = false;
var score;
var habilitarDesabilitarButton = function () {
    if (!CONFIG.get('EMAIL').val() || !CONFIG.get('ENDERECO').val() || !valida) {
        CONFIG.get('BUTTON_PESQ').prop("disabled",true);
        return false;
    }
    CONFIG.get('BUTTON_PESQ').prop("disabled",false);
};
habilitarDesabilitarButton();

//funcao para verificar se o email está correto
var apiMail = function (email) {
    if (!email) {
        return false;
    }
    $.ajax({
        url: 'http://apilayer.net/api/check?access_key=' + CONFIG.get('ACESS_KEY') + '&email=' + email,
        dataType: 'jsonp',
        success: function(json) {
            if (json.format_valid && json.smtp_check) {
                valida = true;
                score = json.score;
                CONFIG.get('EMAIL_RETURN').addClass("success");
            } else {
                valida = false;
                score = "";
                CONFIG.get('EMAIL_RETURN').addClass("error");
            }
            habilitarDesabilitarButton();
        }
    });
};
CONFIG.get('EMAIL').focusout(function() {
    apiMail(jQuery(this).val());
});
CONFIG.get('EMAIL').focusin(function() {
    CONFIG.get('EMAIL_RETURN').removeClass("success");
    CONFIG.get('EMAIL_RETURN').removeClass("error");
});
CONFIG.get('EMAIL').on('input',function() {
    habilitarDesabilitarButton();
});
CONFIG.get('ENDERECO').on('input',function() {
    habilitarDesabilitarButton();
});
CONFIG.get('ENDERECO').focusout(function() {
    habilitarDesabilitarButton();
});

var pesquisar = function (email, endereco) {
    if (!email || !endereco || !valida) {
        alertDanger("Email ou endereço incorretos");
        return false;
    }
    jQuery.post(CONFIG.get('PATH_ROTINA'), { email: email, endereco: endereco })
        .done(function( data ) {
            waitingDialog.hide();
            data = jQuery.parseJSON(data);
            if (data.status == 'false') {
                alertDanger(data.msg);
            } else {
                jQuery("#wrap").load(CONFIG.get('PATH_SUCCESS'), {'numero': data.numero, 'score': score, 'ip': data.ip});
            }
        });
};

CONFIG.get('BUTTON_PESQ').on('click', function () {
    waitingDialog.show('Aguarde..');
    CONFIG.get('BUTTON_PESQ').prop("disabled",true);
    pesquisar(CONFIG.get('EMAIL').val(), CONFIG.get('ENDERECO').val());
});

var alertDanger = function (msg) {
    CONFIG.get('MSG_DANGER').html(msg);
    CONFIG.get('ALERT_DANGER').alert();
    CONFIG.get('ALERT_DANGER').fadeTo(2000, 700).slideUp(700, function(){
        CONFIG.get('ALERT_DANGER').slideUp(700);
    });
};
