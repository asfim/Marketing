/** Custom script */
$(document).ready(function($) {

    // var url = window.location.pathname;
    // var id = url.split("/").splice(-1);
    // alert(id);
    // var urlRegExp = new RegExp(url.replace(/\/$/,''));
    // //alert(urlRegExp);
    // $(".navigation li a").each(function(){
    //     //alert('d');
    //     if(urlRegExp.test(this.href)){
    //
    //         if(id != "") {
    //             $(this).parent().addClass('active');
    //             $(this).parent().closest(".openable").addClass('active');
    //         }else {
    //             $(".navigation li:first-child").addClass("active");
    //         }
    //
    //     }
    // });

    var url = window.location.href;
    var id = url.split("/").splice(-1);
    $(".navigation li a").each(function(){
        //alert('d');
        if(url == this.href){

            if(id != "") {
                $(this).parent().addClass('active');
                $(this).parent().closest(".openable").addClass('active');
            }else {
                $(".navigation li:first-child").addClass("active");
            }

        }
    });


});