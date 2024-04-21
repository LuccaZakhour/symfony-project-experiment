

console.log('storage.js loaded');


function updateDimensions() {
    const rows = document.querySelector('.dimensions-rows').value;
    const cols = document.querySelector('.dimensions-cols').value;

    console.log('Rows:', rows);
    console.log('Cols:', cols);

    if (rows && cols) {
        const dimensions = {
            rows: { numbering: "NUMERIC", count: parseInt(rows) },
            columns: { numbering: "NUMERIC", count: parseInt(cols) }
        };

        // Convert the object to a JSON string
        const dimensionsJson = JSON.stringify(dimensions);

        // Update the inputField with the JSON string
        document.querySelector('.dimensions-field').value = dimensionsJson;
    } else {
        document.querySelector('.dimensions-field').value = '';
    }
}

function setDimensionValuesFromJson(jsonString) {
    // Parse the JSON string
    const dimensions = JSON.parse(jsonString);

    // Extract the row and column counts
    const rowCount = dimensions.rows.count;
    const columnCount = dimensions.columns.count;

    // Find the input fields by their class and set their values
    const rowsInput = document.querySelector('.dimensions-rows');
    const colsInput = document.querySelector('.dimensions-cols');

    if (rowsInput && colsInput) {
        rowsInput.value = rowCount; // Set the rows count
        colsInput.value = columnCount; // Set the columns count
    } else {
        console.error('Row or Column input fields not found');
    }
}



document.addEventListener('DOMContentLoaded', function() {

    document.querySelector('.dimensions-rows').addEventListener('input', updateDimensions);
    document.querySelector('.dimensions-cols').addEventListener('input', updateDimensions);
    let dimensionsField = document.querySelector('.dimensions-field');
    // call setDimensionValuesFromJson
    if (dimensionsField && dimensionsField.value) {
        setDimensionValuesFromJson(dimensionsField.value);
    }
});

