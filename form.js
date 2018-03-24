/**
 * Created by VKabisov on 22.12.2017.
 */


function sendData(inp){
    var file = inp.files[0];

    $.get('/selectel.php', {filename: file.name},function(data){
        var response = JSON.parse(data);

        if (!response.error) {
            $.ajax({
                // Your server script to process the upload
                url: response.url,
                type: 'PUT',

                // Form data
                data: file,

                // Tell jQuery not to process data or worry about content-type
                // You *must* include these options!
                cache: false,
                contentType: false,
                processData: false,

                // Custom XMLHttpRequest
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        // For handling the progress of the upload
                        myXhr.upload.addEventListener('progress', function(e) {
                            $('#progress').text(parseInt((e.loaded*100/e.total)*0.99));
                        } , false);
                    }
                    return myXhr;
                },

                success: function(){
                    $('#progress').text(100);
                }
            });
        }
    });



    /**/
}