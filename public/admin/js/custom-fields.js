document.addEventListener('DOMContentLoaded', function() {
    const fieldTypeSelector = document.getElementById('field-type');
    const fieldNameInput = document.getElementById('field-name');
    const addFieldButton = document.getElementById('add-custom-field');
    const customFieldsContainer = document.getElementById('custom-fields-container');
    const customFieldsJson = document.querySelector('.custom-fields-json');
    const fieldOptionsContainer = document.getElementById('field-options-container');

    let customFields = customFieldsJson.value ? JSON.parse(customFieldsJson.value) : [];

    fieldTypeSelector.addEventListener('change', function() {
        updateFieldOptionsUI(this.value);
    });

    addFieldButton.addEventListener('click', function() {
        const fieldType = fieldTypeSelector.value;
        const fieldName = fieldNameInput.value;
        const options = collectFieldOptions(fieldType);

        const newField = { type: fieldType, name: fieldName, options: options };
        customFields.push(newField);
        updateCustomFieldsJson();
        renderCustomFields();
    });

    function updateFieldOptionsUI(fieldType) {
        // Clear existing options
        fieldOptionsContainer.innerHTML = '';
        // Dynamically add options based on field type
        switch (fieldType) {
            case 'text':
            case 'long_text':
            case 'number':
                // Example for adding a default value input for these types
                addOptionInput('Default Value', 'field-default-value', 'text');
                break;
            case 'date':
                // Add inputs for default date
                addOptionInput('Default Date', 'field-default-date', 'date');
                break;
            case 'datetime':
                // Add inputs for default date, hour, and minute for datetime
                addOptionInput('Default Date', 'field-default-date', 'date');
                addOptionInput('Default Hour (0-23)', 'field-default-hour', 'number');
                addOptionInput('Default Minute (0-59)', 'field-default-minute', 'number');
                break;
            //case 'datetime':
                // Add inputs for default date, format, etc.
                //addOptionInput('Default Date', 'field-default-date', 'date');
                //break;
            case 'dropdown':
            case 'radio':
            case 'checkbox':
                // For choices, you might allow adding multiple choice inputs
                addChoicesOption();
                break;
            case 'file':
                addOptionInput('Allowed File Types (e.g., .jpg,.png)', 'field-file-types', 'text');
                break;
            // Implement other cases as needed
        }
    }

    function addChoicesOption() {
        const container = document.createElement('div');
        container.id = 'choices-container';
        container.innerHTML = `<label>Choices:</label>
                               <div id="choices-list"></div>
                               <button type="button" id="add-choice" class="btn btn-info btn-sm">Add Choice</button>`;
        fieldOptionsContainer.appendChild(container);
    
        document.getElementById('add-choice').addEventListener('click', function() {
            const choiceInput = document.createElement('input');
            choiceInput.type = 'text';
            choiceInput.className = 'form-control choice-input';
            choiceInput.placeholder = 'Choice value';
            document.getElementById('choices-list').appendChild(choiceInput);
        });
    }

    function collectFieldOptions(fieldType) {
        const options = {};
        // Required option
        const isRequired = document.getElementById('field-required')?.checked || false;
        options.required = isRequired;

        // Extend with specific logic for new field types
        document.querySelectorAll('#field-options-container .option-input input').forEach(input => {
            if (input.type === 'checkbox') {
                options[input.id] = input.checked;
            } else {
                options[input.id] = input.value;
            }
        });

        // Handle choices separately due to potential multiple inputs
        if (['dropdown', 'radio', 'checkbox'].includes(fieldType)) {
            options.choices = Array.from(document.querySelectorAll('.choice-input')).map(input => input.value);
        }

        return options;
    }

    function addOptionInput(label, id, type) {
        const wrapper = document.createElement('div');
        wrapper.className = 'option-input';
        const labelElement = document.createElement('label');
        labelElement.textContent = label;
        labelElement.htmlFor = id;

        const inputElement = document.createElement('input');
        inputElement.type = type;
        inputElement.id = id;
        inputElement.className = 'form-control';

        if (type === 'checkbox') {
            labelElement.prepend(inputElement);
            wrapper.appendChild(labelElement);
        } else {
            wrapper.appendChild(labelElement);
            wrapper.appendChild(inputElement);
        }

        fieldOptionsContainer.appendChild(wrapper);
    }

    function getOptionsForType(fieldType) {
        const options = {};
    
        // Common options applicable to all fields
        const isRequired = document.getElementById('field-required')?.checked || false;
        options.required = isRequired;
    
        // Default value input might be reused in multiple field types
        const defaultValueInput = document.getElementById('field-default-value');
        if (defaultValueInput) {
            options.defaultValue = defaultValueInput.value;
        }
    
        // Field-specific options
        switch (fieldType) {
            case 'text':
            case 'long_text':
            case 'number':
                // Example: Add a pattern for validation in case of number
                if (fieldType === 'number') {
                    const patternInput = document.getElementById('field-pattern');
                    if (patternInput) {
                        options.pattern = patternInput.value;
                    }
                }
                break;
            case 'date':
            case 'datetime':
                // For dates, you might want to capture a specific format or default date
                const dateFormatInput = document.getElementById('field-date-format');
                if (dateFormatInput) {
                    options.dateFormat = dateFormatInput.value;
                }
                break;
            case 'dropdown':
            case 'radio':
                // Handle choices for dropdowns and radios
                options.choices = [];
                const choicesContainer = document.getElementById('field-choices-container');
                if (choicesContainer) {
                    const choiceInputs = choicesContainer.querySelectorAll('.field-choice');
                    choiceInputs.forEach(input => {
                        if (input.value) {
                            options.choices.push(input.value);
                        }
                    });
                }
                break;
            case 'checkbox':
                // Checkbox might not need extra options apart from default and required
                break;
            // Implement other cases as needed
        }
    
        return options;
    }
    
    function updateCustomFieldsJson() {
        customFieldsJson.value = JSON.stringify(customFields);
    }

    function renderCustomFields() {
        customFields = customFieldsJson && customFieldsJson.value && customFieldsJson.value !== "null" ? JSON.parse(customFieldsJson.value) : [];

        if (!Array.isArray(customFields)) {
            console.error('customFields is not an array:', customFields);
            customFields = []; // Ensure customFields is an array
        }
    
        customFieldsContainer.innerHTML = customFields.map((field, index) => {
            let optionsHtml = '';
            console.log('-- field', field);
    
            if (field.options && Object.keys(field.options).length > 0) {
                optionsHtml = '<ul>';
                Object.entries(field.options).forEach(([key, value]) => {
                    if (Array.isArray(value) && value.length > 0) {
                        optionsHtml += `<li>${key}: ${value.join(', ')}</li>`;
                    } else if (!Array.isArray(value)) {
                        optionsHtml += `<li>${key}: ${value}</li>`;
                    }
                });
                optionsHtml += '</ul>';
            } else if (Array.isArray(field.options) && field.options.length === 0) {
                optionsHtml = '<p>No specific options provided.</p>';
            }
    
            // Determine if the current field is the last one in the array
            const isLastElement = index === customFields.length - 1;
            // Conditionally apply the `mb-3` class based on whether it's the last element
            const buttonClass = isLastElement ? 'btn btn-danger btn-sm' : 'btn btn-danger btn-sm mb-3';

            return `<div class="custom-field">
                        <strong>${field.name}</strong> (${field.type})
                        ${optionsHtml}
                        <button onclick="removeField(${index})" class="${buttonClass}">Remove</button>
                    </div>`;
        }).join('');
    } 

    window.removeField = function(index) {
        customFields.splice(index, 1);
        updateCustomFieldsJson();
        renderCustomFields();
    };

    // Initialize UI based on current fieldType selection
    updateFieldOptionsUI(fieldTypeSelector.value);


    // Initial rendering
    renderCustomFields();
});
