


// when document loaded
document.addEventListener('DOMContentLoaded', function () {

    console.log('-- work on js tree here');

    showLoader();

    // get API_ENDPOINT from the global scope .env
    const API_ENDPOINT = window.API_ENDPOINT;
    
    console.log('-- API_ENDPOINT', API_ENDPOINT);


    // get the jstree div
    const jstree = document.getElementById('jstree');

    // get the data from the server
    fetch(`${API_ENDPOINT}/api/storage/tree`)
        .then(response => response.json())
        .then(data => {

            hideLoader();

            // create the jstree
            $(jstree).jstree({
                'core': {
                    'data': data
                },
                'plugins': ["themes", "html_data", "search"]
            }).on("select_node.jstree", function (e, data) {
                var href = data.node.a_attr.href; // Assuming you're storing the URL in the a_attr.href
                if(href) {
                    window.location.href = href; // Navigate to the URL
                }
            });
        }).catch(error => {
            console.error('Error loading the tree:', error);
            // Hide the loader and possibly show an error message
            document.getElementById('loader').style.display = 'none';
            // Optionally, update the UI to inform the user that an error occurred
        });
});


