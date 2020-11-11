document.addEventListener('DOMContentLoaded', function () {
    /*
    Ez az event binding lekezel minden ajaxos form-műveletet és választ
    - Az action hidden mezőben benne kell, hogy legyen a formban
    - A válasz szabvány wp_josson_error vagy wp_json_success lehet.
    - wp_json_error esetén a data.errors tömb tartalmazza a hibaüzeneteket, ahol a kulcs a mező neve
    - wp_json_succes lehetséges visszatérései:
    -- data.redirect : átirányít a kapott url-re
    -- data.modal : modal ablak tartalma
    -- data.notification : értesítés (fent középen)
    -- data.script : tetszőleges js függvény (pl loader eltűntetése)
    -- data.content : tartalom lecserélése/hozzáfűzése adott elemhez
        - data.type (prepend | append) : ha nem adunk meg type paramétert, lecseréli az adot ID-jű elem tartamát a data.content-re
        - data.target : a cél elem, aminek a tartalmát manipuláljuk
    -- az egyes paraméterek nem zárják ki egymástegyszerre több paraméter is megadható
    */
    var ajaxForms = document.querySelectorAll('.ajax-form');
	
    for (var ajaxForm of ajaxForms) {
        ajaxForm.addEventListener('submit', function (e) {
            var formElement = e.target;
            e.preventDefault();

            var submitButton = formElement.querySelector('[type=submit]');
            submitButton.disabled = true;
            var buttonCaption = submitButton.querySelector('span');
            var buttonSpinner = submitButton.querySelector('.loader');
            if (buttonCaption) {
                buttonCaption.style.display = 'none';
            }
            if (buttonSpinner) {
                buttonSpinner.style.display = 'inline-block';
            }

            var errors = formElement.querySelectorAll('.uk-form-danger');
            if (errors) {
                for (error of errors) {
                    error.classList.remove('uk-form-danger');
                }
            }

            var errormessages = formElement.querySelectorAll('.form-error');
            if (errormessages) {
                for (error of errormessages) {
                    error.remove();
                }
            }

            var data = new FormData(this);

            var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
            xhr.open('POST', ajaxurl);
            xhr.onreadystatechange = function () {

                submitButton.disabled = false;
                var buttonCaption = submitButton.querySelector('span');
                var buttonSpinner = submitButton.querySelector('.loader');
                if (buttonCaption) {
                    buttonCaption.style.display = 'inline';
                }
                if (buttonSpinner) {
                    buttonSpinner.style.display = 'none';
                }

                if (xhr.readyState > 3 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success === true) {
                        if (response.data.redirect) document.location.href = response.data.redirect;
                        if (response.data.modal) {
                            var modalOptions = JSON.parse(response.data.options);
                            modalOptions = modalOptions || {};
                            UIkit.modal.dialog(response.data.modal, modalOptions);
                        }
                        if (response.data.notification) UIkit.notification(response.data.notification);
                        if (response.data.script) {
                            eval(response.data.script);
                        }
                        if (response.data.content) {
                            if (response.data.type && response.data.type == 'append') {
                                document.getElementById(response.data.target).insertAdjacentHTML('beforeend', response.data.content);
                            } else if (response.data.type && response.data.type == 'prepend') {
                                document.getElementById(response.data.target).insertAdjacentHTML('afterbegin', response.data.content);
                            } else {
                                document.getElementById(response.data.target).innerHTML = response.data.content;
                            }
                        }
                    } else {
                        if (response.data.script) {
                            eval(response.data.script);
                        }
                        if (response.data.notification) UIkit.notification(response.data.notification);
                        if (response.data.errors) {
                            for (var field in response.data.errors) {
                                var elems = formElement.querySelectorAll('[name=' + field + ']');
                                if (elems.length == 0) {
                                    elems = formElement.querySelectorAll('[name="' + field + '[]"]');
                                }
                                for (elem of elems) {
                                    if (elem.type == 'file') {
                                        var next = elem.nextSibling;
                                        next.classList.add('uk-form-danger');
                                        if (response.data.errors[field] != null) {
                                            next.insertAdjacentHTML('afterend', '<span class="form-error uk-text-small uk-text-danger uk-display-block uk-margin-small-top uk-animation-slide-top">' + response.data.errors[field] + '</span>');
                                        }
                                    } else {
                                        elem.classList.add('uk-form-danger');
                                        if (response.data.errors[field] != null) {
                                            elem.insertAdjacentHTML('afterend', '<span class="form-error uk-text-small uk-text-danger uk-display-block uk-margin-small-top uk-animation-slide-top">' + response.data.errors[field] + '</span>');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            };
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.send(data);
        });
    }
});