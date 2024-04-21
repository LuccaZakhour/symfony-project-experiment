

function insertDropdown(parsedData, intValue, positionField, positionValue) {

    if (document.querySelector('.position-dropdown')) {
        // Remove the existing dropdown
        document.querySelector('.position-dropdown').remove();
    }
    
    let positionDropdown = createPositionDropdown(parsedData[intValue], positionValue);

    if (positionField instanceof Element && positionDropdown instanceof Element) {
        // Add the dropdown below the positionField text input
        positionField.insertAdjacentElement('afterend', positionDropdown);
    } else {
        // Position field not found, create a new element and append
        console.log('-- Position field not found. Creating a new element.');
    
        let newContainer = document.createElement('div');
        if (newContainer && newContainer instanceof Node && positionDropdown instanceof Node) {
            newContainer.appendChild(positionDropdown);
            document.body.appendChild(newContainer);
        }
    }
}

function hidePositionField() {
    // Check if an element with .position-dropdown class exists
    let dropdownExists = document.querySelector('.position-dropdown') !== null;

    // If the .position-dropdown exists, hide all .position-field.form-control elements
    if (dropdownExists) {
        let elements = document.querySelectorAll('.position-field.form-control');
        elements.forEach(function(element) {
            element.style.display = 'none';
        });
    }
}

function getPositionsTakenForCurrentStorage() {
    let storageDataField = document.querySelector('.storage-data-field');
    let storagePositionTakenJson = storageDataField.getAttribute('data-position-taken');
    let storagePositionTaken = JSON.parse(storagePositionTakenJson);
    let storageIdElement = document.querySelector('.storage-field .item[data-value]');

    if (storageIdElement) {
        let storageId = storageIdElement.getAttribute('data-value');
        let positionsTakenForCurrentStorage = storagePositionTaken[storageId];

        return positionsTakenForCurrentStorage;
    } else {
        return null;
    }
}

function createPositionDropdown(storageSpec, selectedPosition) {

    let dropdown = document.createElement('select');
    dropdown.classList.add('form-control');
    dropdown.classList.add('position-dropdown'); // Add class for styling if needed

    if (!storageSpec || storageSpec.rows === undefined || storageSpec.columns === undefined) {
        return;
    }

    let rows = storageSpec.rows.count;
    let columns = storageSpec.columns.count;

    let positionsTakenForCurrentStorage = getPositionsTakenForCurrentStorage();
    console.log('1# *** positionsTakenForCurrentStorage', positionsTakenForCurrentStorage);

    for (let row = 1; row <= rows; row++) {
        for (let col = 1; col <= columns; col++) {
            let option = document.createElement('option');
            
            option.value = (row - 1) * columns + col; // Calculating position index
            option.textContent = 'Row ' + row + ' - Col ' + col;

            if (!positionsTakenForCurrentStorage.includes(option.value.toString())) {
                dropdown.appendChild(option);
            }
        }
    }

    // Set the selected value if applicable
    if (selectedPosition) {
        dropdown.value = selectedPosition;
    }

    return dropdown;
}

function storageInit() {
    // get storageSpec from parsedData by storageField id from child div with class="item" and storageField id for example 856 from data-value="856"
    let selector = '.storage-field .item[data-value]';
    let storageIdElement = document.querySelector(selector);

    if (storageIdElement) {
        // Element found, get the data-value attribute
        dataValue = storageIdElement.getAttribute('data-value');
        intValue = parseInt(dataValue, 10); // Parse the value as an integer

        return [intValue, dataValue];
    } else {
        console.log('Element not found');
    }
}


function listenToPositionFieldDropdownChange() {
    const selectElement = document.querySelector('.position-dropdown');
    if (selectElement) {
        selectElement.addEventListener('change', function() {
            const selectedValue = this.value;
            const positionField = document.querySelector('.position-field');

            if (positionField) {
                // If positionField is an input or similar element
                if (positionField.value !== undefined) {
                    positionField.value = selectedValue;
                }
                // If positionField is a div, span, etc.
                else {
                    positionField.textContent = selectedValue;
                }
            } else {
                console.error('Position field not found');
            }
        });
    }
}

function dorpdownListeners(parsedData, dataValue, positionField, positionValue) {
    insertDropdown(parsedData, dataValue, positionField, positionValue);
    hidePositionField();
    listenToPositionFieldDropdownChange();
}



// on loaded
document.addEventListener("DOMContentLoaded", function(event) {
    
    // select .position-field
    let positionField = document.querySelector('.position-field');
    let positionValue = parseInt(positionField.value);
    
    let storageDataField = document.querySelector('.storage-data-field');

    // get from attr data-storage-values
    let storageDataFieldDataStorageValues = storageDataField.getAttribute('data-storage-values');

    // parse to json
    let storageDataFieldDataStorageValuesObj = JSON.parse(storageDataFieldDataStorageValues);

    console.log('-- storageDataFieldDataStorageValuesObj', storageDataFieldDataStorageValuesObj);

    let parsedData = {};
    for (let key in storageDataFieldDataStorageValuesObj) {
        if (storageDataFieldDataStorageValuesObj.hasOwnProperty(key)) {
            try {
                parsedData[key] = JSON.parse(storageDataFieldDataStorageValuesObj[key]);
            } catch (e) {
                parsedData[key] = storageDataFieldDataStorageValuesObj[key];
            }
        }
    }

    let [intValue, dataValue] = storageInit() || [];
    
    // Select the target node
    let storageFieldElement = document.querySelector('.storage-field');

    // element on change element
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === "childList") {
                // Your logic here
                // For example, re-initializing the dropdown insertion and event listeners
                const [intValue, dataValue] = storageInit() || [];
                const positionField = document.querySelector('.position-field');
                const positionValue = parseInt(positionField.value, 10);
                
                dorpdownListeners(parsedData, dataValue, positionField, positionValue)
            }
        });
    });

    // Configuration of the observer:
    const config = { childList: true };

    // Start observing:
    observer.observe(storageFieldElement, config);
    
    dorpdownListeners(parsedData, dataValue, positionField, positionValue)
});


document.addEventListener('DOMContentLoaded', function() {

    listenToPositionFieldDropdownChange();
});


document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('grid-container'); // Ensure this ID is on your HTML
    generateGrid(container, 9, 9); // Generates a 9x9 grid
    let positionsTakenForCurrentStorage = getPositionsTakenForCurrentStorage();
    console.log('1# *** positionsTakenForCurrentStorage', positionsTakenForCurrentStorage);
});

function generateGrid(container, numRows, numCols) {
    for (let row = 1; row <= numRows; row++) {
        for (let col = 1; col <= numCols; col++) {
            const cell = document.createElement('div');
            cell.className = 'grid-item';
            cell.dataset.position = `${row}x${col}`;
            cell.addEventListener('click', function() {
                if (!this.classList.contains('taken')) {
                console.log('Position selected:', this.dataset.position);
                }
            });
            cell.addEventListener('mouseenter', function() {
                const hoverText = document.querySelector('.hover-text');
                if (hoverText) { // Check if hoverText exists to avoid null reference errors
                hoverText.textContent = `Box ${row}x${col}`; // Set hover text
                hoverText.style.display = 'block'; // Show hover text
                hoverText.style.left = `${event.pageX + 10}px`; // Position dynamically
                hoverText.style.top = `${event.pageY}px`;
                }
            });
            cell.addEventListener('mouseleave', function() {
                const hoverText = document.querySelector('.hover-text');
                if (hoverText) { // Ensure hoverText exists
                hoverText.style.display = 'none'; // Hide hover text
                }
            });
            container && container.appendChild(cell); // Append each cell to the container
        }
    }
}
  

