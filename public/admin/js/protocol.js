

document.addEventListener('DOMContentLoaded', function() {
    var trashButtons = Array.from(document.querySelectorAll("button")).filter(button => button.querySelector("i.fa-trash-alt"));

    trashButtons.forEach(function(trashButton) {
        var moveUpButton = document.createElement("button");
        moveUpButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
        moveUpButton.style.position = 'absolute';
        moveUpButton.style.color = 'blue';
        moveUpButton.style.right = '30px';
        moveUpButton.style.top = '1px';
        moveUpButton.classList.add("move-up", "btn", "btn-link");

        var moveDownButton = document.createElement("button");
        moveDownButton.innerHTML = '<i class="fas fa-arrow-down"></i>';
        moveDownButton.style.position = 'absolute';
        moveDownButton.style.color = 'blue';
        moveDownButton.style.right = '55px';
        moveDownButton.style.top = '1px';
        moveDownButton.classList.add("move-down", "btn", "btn-link");

        trashButton.parentNode.insertBefore(moveDownButton, trashButton);
        trashButton.parentNode.insertBefore(moveUpButton, trashButton);
    });
});



class Protocol {
    // Define the openProtocol method
    static openProtocol(description, id) {
        // Implement your logic here
        console.log(`Opening protocol with description: ${description} and ID: ${id}`);
        // For example, you might want to navigate to a new URL based on the inputs
        // window.location.href = `/protocols/${id}?desc=${encodeURIComponent(description)}`;

        const baseUrl = "?crudAction=detail&crudControllerFqcn=App%5CController%5CAdmin%5CProtocolCrudController";
    
        // Construct the full URL by appending the entityId
        const fullUrl = `${baseUrl}&entityId=${id}`;
        
        // Set window.location.href to navigate to the URL
        window.location.href = fullUrl;
    }
}

// Assuming you want to trigger the method upon clicking the anchor tag
// You need to ensure this code runs after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', (event) => {
    // Find the anchor tag by its href (assuming it's unique)
    const anchor = document.querySelector('a[href^="javascript:Protocol.openProtocol"]');
    if (anchor) {
        // Override the click event for the anchor
        anchor.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent the default anchor action
            // Extract the parameters from the href attribute
            const match = anchor.getAttribute('href').match(/'([^']*)',(\d+)/);
            if (match) {
                // Call the openProtocol method with extracted arguments
                Protocol.openProtocol(match[1], parseInt(match[2], 10));
            }
        });
    }
});

