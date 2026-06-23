<?php
/**
 * template_end.php
 *
 * Author: pixelcave
 *
 * The last block of code used in every page of the template
 *
 * We put it in a separate file for consistency. The reason we
 * separated template_scripts.php and template_end.php is for enabling us
 * put between them extra javascript code needed only in specific pages
 *
 */
?>
<script>
$(document).ready(function(){
				   var $loading = $('#loader').hide();

                   //Attach the event handler to any element
                   jQuery(document).ajaxStart(function () {
                   		//alert("start");
                        //ajax request went so show the loading image
                         $loading.show();
                     }).ajaxStop(function () {
						//alert("stop");
                       //got response so hide the loading image
                        $loading.hide();
                    });
    });
$(function() { FormsValidation.init(); });</script>
    </body>
</html>