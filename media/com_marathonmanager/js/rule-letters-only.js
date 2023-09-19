document.addEventListener('DOMContentLoaded', function() {
    // Note: setHandler('letter'...) is used where the field class contains 'validate-letter'
    'use strict';
    setTimeout(() => {
        if(document.hasOwnProperty('formvalidator')){
            document.formvalidator.setHandler('letter', (value)=>{
                const regex = /^[a-z]+$/i;
                return regex.test(value);
            })
        }
    }, (1000));
});