/**
 * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
((document, submitForm) => {

  // Selectors used by this script
  const buttonDataSelector = 'data-submit-task';
  const formId = 'adminForm';

  /**
   * Submit the task
   * @param task
   */
  const submitTask = task => {
    const form = document.getElementById(formId);
    if (task === 'registration.clear' || document.formvalidator.isValid(form)) {
      submitForm(task, form);
    }else{
        // Scroll to first element with error class "invalid" including an offset of 100px
        const invalid = document.querySelector('.invalid');
        if (invalid) {
          window.scrollTo({
            behavior: 'smooth',
            top:
                invalid.getBoundingClientRect().top -
                document.body.getBoundingClientRect().top -
                150,
          })
        }
    }
  };

  // Register events
  document.addEventListener('DOMContentLoaded', () => {
    const buttons = [].slice.call(document.querySelectorAll(`[${buttonDataSelector}]`));
    buttons.forEach(button => {
      button.addEventListener('click', e => {
        e.preventDefault();
        const task = e.target.getAttribute(buttonDataSelector);
        submitTask(task);
      });
    });
  });
})(document, Joomla.submitform);