

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.grid-modal-trigger').forEach(trigger => {
        trigger.addEventListener('click', function() {
            let targetModalId = trigger.getAttribute('data-bs-target');
            let targetModal = document.querySelector(targetModalId);
            let gridContainer = targetModal.querySelector('.grid-container');
            
            let dimensions = gridContainer.getAttribute('data-dimensions');
            const { numRows, numCols } = parseDimensions(dimensions);

            if (numRows > 0 && numCols > 0) {
                let takenPositions;
                let key = targetModalId.substring(1);
                
                if (window.takenPositionsWindow && key in window.takenPositionsWindow) {
                    takenPositions = window.takenPositionsWindow[key];
                } else {
                    console.error('Key not found:', key);
                    takenPositions = []; // Fallback to an empty array
                }
                generateGrid(gridContainer, numRows, numCols, takenPositions);
            }
        });
    });
});

// Function to generate labels based on type and index
function getLabel(index, labelType) {
    switch (labelType) {
        case 'numeric':
            return index + 1;
        case 'alphabetic':
            return String.fromCharCode(65 + index);
        case 'roman':
            return convertToRoman(index + 1);
        case 'none':
            return '';
        default:
            return index + 1;
    }
}

// Helper function for converting numbers to Roman numerals (if needed)
function convertToRoman(num) {
    // Implement conversion logic here
    // ...
}

function parseDimensions(dimensions) {
    if (!dimensions) {
        console.error("Invalid dimensions value");
        return { numRows: 0, numCols: 0 };
    }

    const parts = dimensions.split('x');
    if (parts.length === 2) {
        const numRows = parseInt(parts[0]);
        const numCols = parseInt(parts[1]);
        if (!isNaN(numRows) && !isNaN(numCols)) {
            return { numRows, numCols };
        }
    }
    return { numRows: 0, numCols: 0 };
}

function generateGrid(container, numRows, numCols, takenPositions = []) {
    
    takenPositions = Array.isArray(takenPositions) ? takenPositions.map(pos => parseInt(pos, 10)) : [];
    
    if (!container) {
        console.error("Container element not found");
        return;
    }

    // for debug
    //console.log("-- Generating grid with dimensions:", numRows, numCols);
    //console.log('-- takenPositions', takenPositions);

    container.innerHTML = ''; // Clear previous grid items
    container.style.gridTemplateColumns = `repeat(${numCols}, 1fr)`;

    let positionIndexCount = 1;

    for (let row = 1; row <= numRows; row++) {
        for (let col = 1; col <= numCols; col++) {
            const position = `${row}x${col}`; // This matches your takenPositions format
            const cell = document.createElement('div');
            cell.className = 'grid-item';
            cell.dataset.position = position;
            cell.textContent = position;

            // Check if position is taken and mark it
            if (takenPositions.includes(positionIndexCount)) {
                cell.classList.add('taken');
            }

            container.appendChild(cell);

            positionIndexCount++;
        }
    }
}

