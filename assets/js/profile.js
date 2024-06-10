document.addEventListener('DOMContentLoaded', function () {
    const isCompanyCheckbox = document.getElementById('profile_isCompany');
    const companyNameField = document.getElementById('profile_companyName').closest('.mb-3');
    const siretNumberField = document.getElementById('profile_siretNumber').closest('.mb-3');

    function toggleCompanyFields() {
        if (isCompanyCheckbox.checked) {
            companyNameField.style.display = '';
            siretNumberField.style.display = '';
        } else {
            companyNameField.style.display = 'none';
            siretNumberField.style.display = 'none';
        }
    }

    // Initial check
    toggleCompanyFields();

    // Add event listener
    isCompanyCheckbox.addEventListener('change', toggleCompanyFields);
});