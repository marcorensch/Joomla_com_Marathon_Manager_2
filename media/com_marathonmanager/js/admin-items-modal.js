;(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const elements = document.querySelectorAll('.select-link');

        for (let i = 0; i < elements.length; i++) {
            elements[i].addEventListener('click', function (e) {
                e.preventDefault();

                const functionName = e.target.dataset.function;

                window.parent[functionName](
                    e.target.dataset.id,
                    e.target.dataset.title,
                    null,
                    null,
                    e.target.dataset.uri,
                    e.target.dataset.language,
                    null
                )

                if (window.parent.Joomla.Modal) {
                    window.parent.Joomla.Modal.getCurrent().close();
                }
            })
        }
    });
})();