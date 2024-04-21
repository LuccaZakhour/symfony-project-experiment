

// if document loaded
document.addEventListener('DOMContentLoaded', function() {

    if (typeof CKEDITOR === 'undefined') {
        var script = document.createElement('script');
        script.src = 'https://cdn.ckeditor.com/4.16.0/full/ckeditor.js';
        script.onload = function() {
            initEditor(); // Initialize the editor once CKEditor script is loaded
        };
        document.head.appendChild(script);
    } else {
        // If CKEditor is already loaded, directly initialize the editor
        initEditor();                                           
    }
});

function initEditor() {

    // Initialize CKEditor on the specific textarea
    var textareas = document.querySelectorAll('.my-ckeditor-textarea');

    try {
        // Initialize CKEditor for each textarea
        textareas.forEach(function(textarea) {
            if (!CKEDITOR.instances[textarea.id]) {
                CKEDITOR.replace(textarea, {
                    // Remove upload-related plugins
                    removePlugins: 'filebrowser, uploadfile, exportpdf',// uploadimage, imageupload, uploadwidget
            
                    // Define a standard toolbar without upload buttons
                    toolbar: [
                        { name: 'document', items: ['Source'] },
                        { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                        { name: 'editing', items: ['Scayt'] },
                        '/',
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
                        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'] },
                        { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                        '/',
                        { name: 'insert', items: ['Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'] },
                        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                        { name: 'colors', items: ['TextColor', 'BGColor'] },
                        { name: 'tools', items: ['Maximize', 'ShowBlocks'] },
                        { name: 'others', items: ['-'] },
                        { name: 'about', items: ['About'] }
                    ],

                    //filebrowserUploadUrl: '${API_ENDPOINT}/api/image/upload'
                });
            } else {
                console.log('CKEditor instance already exists for:', textarea.id);
            }
        });
    } catch (error) {
        //console.log(error);
    }
}


// Wait for the DOM to be ready
document.addEventListener("DOMContentLoaded", function(event) {

    const API_ENDPOINT = window.API_ENDPOINT;

    initEditor();
});

document.addEventListener('DOMContentLoaded', function() {
    // Select the button with the specified classes
    let addButton = document.querySelector('.btn.btn-link.field-collection-add-button');
    let accordionBtn = document.querySelector('.accordion-button');

    // Check if the button exists
    if (addButton || accordionBtn) {
        console.log('Button found');
        // Add event listener for 'click' event
        addButton && addButton.addEventListener('click', function() {
            // Your code to execute when the button is clicked  
            setTimeout(function() {
                initEditor();
            }, 500);
        });

        accordionBtn && accordionBtn.addEventListener('click', function() {
            // Your code to execute when the button is clicked  
            setTimeout(function() {
                initEditor();
            }, 500);
        });
    } else {
        console.log('Button not found');
    }
});