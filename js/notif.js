$(document).ready(function() {
	checknotif();
});
function checknotif() {
	if (!Notification) {
		//$('body').append('<h4 style="color:red">*Browser does not support Web Notification</h4>');
		return;
	}
	if (Notification.permission !== "granted")
		Notification.requestPermission();
	else {
		  							 // $.ajax({
            //                             url : 'php_actions/notification.php',
            //                             cache: false,
            //                             processData: false,
            //                             success:function(response) {
            //                             		console.log(response);
            //                                     var response = JSON.parse(response);
												// 	var theurl = "my-leads.php";
												// 	var notifikasi = new Notification("Breeze Update", {
												// 		icon: "https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310-150x150.png",
												// 		body: response['data'],
												// 	});
												// 	notifikasi.onclick = function () {
												// 	window.open(theurl); 
												// 	notifikasi.close();     
												// 	};
												// 	setTimeout(function(){
												// 		notifikasi.close();
												// 	}, 5000);
            //                             }
            //                         });

	}
};