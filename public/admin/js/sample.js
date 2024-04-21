
console.log('sample.js');

document.addEventListener('DOMContentLoaded', function() {
    const createSampleSeriesCheckbox = document.getElementById('Sample_createSampleSeries');
    const form = document.querySelector('form');

    const descriptionField = document.getElementById('Sample_description');
    const barcodeField = document.getElementById('Sample_barcode');
    const positionField = document.getElementById('Sample_position');
    const sampleCountsField = document.getElementById('Sample_sampleCounts');
    const userField = document.getElementById('Sample_user');
    const createdAtField = document.getElementById('Sample_createdAt');

    descriptionField.closest('.form-group').style.display = 'block';
    barcodeField.closest('.form-group').style.display = 'block';
    positionField.closest('.form-group').style.display = 'block';
    sampleCountsField.closest('.form-group').style.display = 'none';
    userField.closest('.form-group').style.display = 'block';
    createdAtField.closest('.form-group').style.display = 'block';

    createSampleSeriesCheckbox.addEventListener('change', function() {
        if (this.checked) {
            descriptionField.closest('.form-group').style.display = 'none';
            barcodeField.closest('.form-group').style.display = 'none';
            positionField.closest('.form-group').style.display = 'none';
            sampleCountsField.closest('.form-group').style.display = 'block';
            userField.closest('.form-group').style.display = 'none';
            createdAtField.closest('.form-group').style.display = 'none';

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const name = document.getElementById('Sample_name').value;
                const sampleType = document.getElementById('Sample_sampleType').value;
                const sampleCounts = document.getElementById('Sample_sampleCounts').value;

                const formData = new FormData(form);
                formData.append('sample[createSampleSeries]', true);

                fetch ('/sample/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        createSampleSeries: true,
                        name: name,
                        sampleType: sampleType,
                        sampleCounts: sampleCounts,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    console.log('data data');
                    console.log(data);
                    if (data.url) {
                        window.location.href = data.url;
                    } else {
                        console.log('Error: No URL provided in response');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                })
            });
        } else {
            descriptionField.closest('.form-group').style.display = 'block';
            barcodeField.closest('.form-group').style.display = 'block';
            positionField.closest('.form-group').style.display = 'block';
            sampleCountsField.closest('.form-group').style.display = 'none';
            userField.closest('.form-group').style.display = 'block';
            createdAtField.closest('.form-group').style.display = 'block';
        }
    });
});
