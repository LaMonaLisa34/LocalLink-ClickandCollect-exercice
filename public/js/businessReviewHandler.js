var bool = true;

document.addEventListener("DOMContentLoaded", function () {

    const checkboxes = document.querySelectorAll('input[name="selected_reviews[]"]');
    const selectAll = document.getElementById('select-all');

    if (selectAll && checkboxes) {
        selectAll.addEventListener('click', () => {
            if (bool === true) {
                checkboxes.forEach(checkbox => checkbox.checked = bool);
                bool = false;
            }
            else {
                checkboxes.forEach(checkbox => checkbox.checked = bool);
                bool = true;
            }
        });
    }
});





