
document.getElementById('form-demo-registration').addEventListener('submit', function(event){
    event.preventDefault();
    alert('demo');
    const API_ENDPOINT = window.API_ENDPOINT;
    var formData = new FormData(this);
    var jsonData = {};
    formData.forEach(function(value, key){
        jsonData[key] = value;
    });
    fetch('/api/demo_register/register', {
        method:'POST',
        body: JSON.stringify(jsonData),
        headers: {
            'Content-Type' : 'application/json'
        }
    })
    .then(function(response){
        if(!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(function(data){
        console.log(data);
    })
    .catch(function(error){
        console.error('There was a problem with your fetch operation', error);
    })
});