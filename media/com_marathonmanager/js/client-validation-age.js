document.addEventListener('DOMContentLoaded', function() {
    console.log('client-validation-age.js loaded');
    'use strict';
    // OnKeyUp event for the age field

    if(document.formvalidator){
        document.formvalidator.setHandler('age', function(value, element) {
            const regex = /^\d{4}$/;
            if(!regex.test(value)){
                element.dataset.validationText = 'Please enter a valid year of birth (4 digits)';
                return false;
            }

            value = parseInt(value);
            let min = parseInt(element.getAttribute('min')) || 1940;
            let max = parseInt(element.getAttribute('max')) || new Date().getFullYear();

            // Check that the value is a number with four digits and is between the min and max values
            if(isNaN(value) || value < 999 || value > 9999){
                element.dataset.validationText = 'Please enter a valid year of birth';
                return false;
            }
            if(value < min){
                element.dataset.validationText = `The first year of birth is ${min}`;
                return false;
            }
            if(value > max){
                const minimumAge =  new Date().getFullYear() - max + 1;
                element.dataset.validationText = 'Minimum Age is ' + minimumAge + ' years';
                return false;
            }

            return true;
        });
    }
})