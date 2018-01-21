document.addEventListener("touchstart", function() {},false);


var clickOrTouch = (('ontouchend' in window)) ? 'touchend' : 'click';
$('.num_btn').on(clickOrTouch, function() {
    console.log('adding number '+$(this).text());
    addNumber($(this).text());
});

$('#btnClear').on(clickOrTouch, function() {
    clearForm();
});

$('#btnGo').on(clickOrTouch, function() {
    submitForm();
});


function addNumber(e){
    //document.getElementById('PINbox').value = document.getElementById('PINbox').value+element.value;
    var d = $( "#PINdisplay" ).text();
    $( "#PINdisplay").text(d + "*");
    var v = $( "#PINbox" ).val();
    $( "#PINbox" ).val( v + e );
}
function clearForm(e){
    //document.getElementById('PINbox').value = "";
    $( "#PINdisplay" ).html("&nbsp;");
    $( "#PINbox" ).val( "" );
}
function submitForm() {
    // Set the device fingerprint value for authentication
    var fp = new Fingerprint().get();
    console.log('fingerprint:'+fp);
    $('#fingerprint').val(fp);

    // Ensure the user entered a PIN
    if ($('#PINbox').val() == "") {
        alert("Enter a PIN");
    } else {
        data = {
            pin: $('#PINbox').val(),
            fingerprint: fp
        }
    // Prevent default posting of form - put here to work in case of errors
    event.preventDefault();

    // Abort any pending request
    try{
        if (request) {
            request.abort();
        }
    }catch(err){
    }
    // setup some local variables
    var $form = $("#PINform");
    var $inputs = $form.find("input");

    // Serialize the data in the form
    var data = {};
    data.pin = $("#PINbox").val();
    data.fingerprint = fp;
    //var serializedData = $form.serialize();
    // Let's disable the inputs for the duration of the Ajax request.
    // Note: we disable elements AFTER the form data has been serialized.
    // Disabled form elements will not be serialized.
    $inputs.prop("disabled", true);


    // Fire off the request to /service/auth.php
    request = $.ajax({
        url: "/service/auth.php",
        type: "post",
        data: data
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
        // Log a message to the console
        if(response == "1")
        {
            window.location.replace("/alarm.php");
        }
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // Log the error to the console
        console.error(
            "The following error occurred: "+
            textStatus, errorThrown
        );
    });

    // Callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        // Reenable the inputs
        $inputs.prop("disabled", false);
    });
        //document.getElementById('PINbox').value = "";
        $( "#PINbox" ).val( "" );
    };
};
