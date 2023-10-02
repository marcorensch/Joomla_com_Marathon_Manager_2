jQuery(document).ready(function(){
    console.log("rule-checked.js");
    document.formvalidator.setHandler("checked", function (value, element) {
        console.log(value == "1");
        return value == "1";
    });
});