document.addEventListener('DOMContentLoaded', function () {
    const registrations = document.querySelectorAll('.registration-card');
    registrations.forEach(function (registration) {
        const registrationHeader = registration.querySelector('.nxd-registration-header');
        const registrationDetails = registration.querySelector('.nxd-registration-details');
        // Set height to 0 of registration details
        registrationDetails.style.height = '0px';

        registrationHeader.addEventListener('click', function () {
            if (registrationDetails.style.height === '0px') {
                // Animates the height of the registration details
                registrationDetails.animate([
                    { height: '0px' },
                    { height: registrationDetails.scrollHeight + 'px' }
                ], {
                    duration: 300,
                    easing: 'ease-in-out'
                });
                registrationDetails.style.height = registrationDetails.scrollHeight + 'px';
            } else {
                // Animates the height of the registration details
                registrationDetails.animate([
                    { height: registrationDetails.scrollHeight + 'px' },
                    { height: '0px' }
                ], {
                    duration: 300,
                    easing: 'ease-in-out'
                });
                registrationDetails.style.height = '0px';
            }
        });
    });
});