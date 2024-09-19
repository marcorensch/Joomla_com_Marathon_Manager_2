document.addEventListener('DOMContentLoaded', function() {
    console.log('client-validation-age.js loaded');
    'use strict';
    // OnKeyUp event for the age field

    if(document.formvalidator){
        document.formvalidator.setHandler('age', function(value, element) {
            const regex = /^\d{4}$/;
            if(!regex.test(value)){
                element.dataset.validationText = Joomla.Text._("COM_MARATHONMANAGER_FIELD_BIRTHYEAR_INVALID_FORMAT");
                return false;
            }

            value = parseInt(value);
            let min = parseInt(element.getAttribute('min')) || 1940;
            let max = parseInt(element.getAttribute('max')) || new Date().getFullYear();
            let min_age = parseInt(element.getAttribute('data-min-age')) || new Date().getFullYear();

            if(value < min){
                element.dataset.validationText = Joomla.Text._("COM_MARATHONMANAGER_FIELD_BIRTHYEAR_TOO_SMALL") + min;
                return false;
            }
            if(value > max){
                element.dataset.validationText = Joomla.Text._("COM_MARATHONMANAGER_FIELD_BIRTHYEAR_TOO_LARGE") + min_age;
                return false;
            }

            return true;
        });
    }
})