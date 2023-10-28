// Version: 1.0
document.addEventListener('DOMContentLoaded', function() {
    document.formvalidator.setHandler("checked", function (value, element) {
        return value === "1" && element.checked || value === "0" && !element.checked;
    });
});