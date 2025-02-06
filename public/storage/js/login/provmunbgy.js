document.addEventListener("DOMContentLoaded", function () {

    var provinceUrl = 'http://127.0.0.1:8000/storage/json/table_province.json';
    var municipalityUrl = 'http://127.0.0.1:8000/storage/json/table_municipality.json';
    var barangayUrl = 'http://127.0.0.1:8000/storage/json/table_barangay.json';

    Promise.all([
        fetch(provinceUrl).then(response => response.json()).catch(() => []),
        fetch(municipalityUrl).then(response => response.json()).catch(() => []),
        fetch(barangayUrl).then(response => response.json()).catch(() => [])
    ])
    .then(([provinces, municipalities, barangays]) => {
       
        provinces.sort((a, b) => a.province_name.localeCompare(b.province_name));

        const provinceSelect = document.getElementById('prov');
        const municipalitySelect = document.getElementById('municity');
        const barangaySelect = document.getElementById('bgy');

        let defaultProvinceId = null;
        let defaultMunicipalityId = null;
        let defaultBarangayId = null;   

        // Populate Province Dropdown
        provinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province.province_name; 
            option.dataset.id = province.province_id; 
            option.textContent = province.province_name;

            if (province.province_name === "Southern Leyte") {
                option.selected = true;
                defaultProvinceId = province.province_id;
            }

            provinceSelect.appendChild(option);
        });

        provinceSelect.addEventListener('change', function () {
            const provinceId = parseInt(this.selectedOptions[0].dataset.id); 
            updateMunicipality(provinceId);
        });

        function updateMunicipality(provinceId) {
            municipalitySelect.innerHTML = '<option value="">Select Municipality/City</option>';
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

            const filteredMunicipalities = municipalities
                .filter(municipality => municipality.province_id === provinceId)
                .sort((a, b) => a.municipality_name.localeCompare(b.municipality_name));

            filteredMunicipalities.forEach(municipality => {
                const option = document.createElement('option');
                option.value = municipality.municipality_name; 
                option.dataset.id = municipality.municipality_id; 
                option.textContent = municipality.municipality_name;

                if (municipality.municipality_name === "Sogod") {
                    option.selected = true;
                    defaultMunicipalityId = municipality.municipality_id;
                }

                municipalitySelect.appendChild(option);
            });

        }

        municipalitySelect.addEventListener('change', function () {
            const municipalityId = parseInt(this.selectedOptions[0].dataset.id);
            updateBarangay(municipalityId);
        });

        function updateBarangay(municipalityId) {
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

            const filteredBarangays = barangays
                .filter(barangay => barangay.municipality_id === municipalityId)
                .sort((a, b) => a.barangay_name.localeCompare(b.barangay_name));

            filteredBarangays.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay.barangay_name; 
                option.dataset.id = barangay.barangay_id; 
                option.textContent = barangay.barangay_name;
                barangaySelect.appendChild(option);
            });
        }

        // Set default province
        if (defaultProvinceId) {
            updateMunicipality(defaultProvinceId);
        }

        // Set default municipality
        if (defaultMunicipalityId) {
            updateBarangay(defaultMunicipalityId);
        }

    })
    .catch(error => console.error('Error loading JSON files:', error));
});
