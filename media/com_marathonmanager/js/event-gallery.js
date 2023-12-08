document.addEventListener("DOMContentLoaded", function () {
    // Get the src tag value of all images that are not loaded by thumbnail
    const imagesToCreateThumbnails = document.querySelectorAll("#event-gallery .event-gallery-image:not(.event-gallery-image-thumbnail)");
    const imageUris = [];
    imagesToCreateThumbnails.forEach(function (image) {
        imageUris.push(image.getAttribute("src"));
    });
    const token = document.querySelector("input[name=gallery-token]").value;

    if(!imageUris.length) {
        return;
    }
    console.log("Creating Thumbnails");
    // Create AJAX request to create thumbnails
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/index.php?option=com_marathonmanager&task=gallery.createThumbnails&format=json");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send("width=400&images=" + JSON.stringify(imageUris) + "&" + token + "=1");
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const response = JSON.parse(this.responseText);
            console.log(response.message);
        }
    };
});